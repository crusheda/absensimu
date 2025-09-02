<?php

namespace App\Http\Controllers\Android;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\profil_rs;
use App\Models\users;
use App\Models\absensi;
use App\Models\jadwal;
use App\Models\jadwal_detail;
use App\Models\ref_shift;
use App\Models\ref_users;
use Jenssegers\Agent\Agent;
use Carbon\Carbon;
use Auth,Validator,Redirect,Response,File,Storage;

class AbsensiController extends Controller
{
    public function lokasiKantor()
    {
        $profil = profil_rs::first(); // atau pakai model kalau ada

        return response()->json([
            'latitude' => (float) $profil->coord_lat,
            'longitude' => (float) $profil->coord_long,
        ]);
    }

    function init(Request $request)
    {
        // DESCRIBE POST VALUE
        $user = $request->id_user;
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $jarak = (int) $request->jarak;

        // DESCRIBE POST OUTPUT
        $message = 'Tidak ada.';
        $keterangan = '';
        $btn_pulang = false;
        $btn_berangkat = false;
        $btn_ijin = false;

        // INIT VAL
        $profil_rs = profil_rs::first();
        $now = Carbon::now();
        $datenow = $now->format('Y-m-d');
        $datetommorow = $now->copy()->addDay()->format('Y-m-d'); // hasil: "2025-06-29"
        $tahun = $now->format('Y'); // 2025
        $bulan = $now->format('m'); // 06
        $tgl   = $now->format('j'); // 3
        $th = $now->hour;           // Jam (0–23)
        $tm = $now->minute;         // Menit (0–59)
        $ts = $now->second;         // Detik (0–59)
        $hit = "tgl".$tgl;

        // STARTING QUERY DATA
        /////////////////////////////////// SHIFT & ABSENSI ///////////////////////////////////
        $jadwal = jadwal_detail::leftJoin('kepegawaian_jadwal', function($join) {
                            $join->on('kepegawaian_jadwal.id', '=', 'kepegawaian_jadwal_detail.id_jadwal')
                                ->whereNull('kepegawaian_jadwal.deleted_at');
                        })
                        ->select('kepegawaian_jadwal_detail.'.$hit,'kepegawaian_jadwal.pegawai_id as atasan','kepegawaian_jadwal.staf as bawahan','kepegawaian_jadwal.progress')
                        // ->whereJsonContains('kepegawaian_jadwal.staf', $request->user)
                        ->where('kepegawaian_jadwal_detail.pegawai_id',$user)
                        ->where('kepegawaian_jadwal.bulan',$bulan)
                        ->where('kepegawaian_jadwal.tahun',$tahun)
                        ->whereIn('kepegawaian_jadwal.progress',[1,2,3])
                        ->whereNull('kepegawaian_jadwal_detail.deleted_at')
                        ->orderBy('kepegawaian_jadwal_detail.id','DESC')
                        ->first();

        $callDistance = $this->distance($profil_rs->coord_lat, $profil_rs->coord_long, $latitude, $longitude);
        $distance = round($callDistance["meters"]);

        // PENENTUAN TOMBOL ABSENSI
        $show = absensi::where('pegawai_id',$user)
                        // ->whereDate("tgl_in","=",$datenow)
                        ->whereDate("ref_jam_masuk","=",$datenow)
                        ->where("jenis",'1') // SHIFT
                        ->orderBy("tgl_in","DESC")
                        ->first();
        $showMalam = absensi::where('pegawai_id',$user)
                        ->whereDate("ref_jam_pulang","=",$datenow)
                        ->where("tgl_out",null)
                        ->where("lewat_hari",'1')
                        ->where("jenis",'1')
                        ->orderBy("ref_jam_pulang","DESC")
                        ->first();
        $ijin = absensi::where('pegawai_id',$user)
                        ->whereDate("tgl_in","=",$datenow)
                        ->where("jenis",'3') // IJIN SAKIT
                        ->orderBy("tgl_in","DESC")
                        ->first();
        $dinasLuar = absensi::where('pegawai_id',$user)
                        ->whereDate("tgl_in","=",$datenow)
                        ->where("jenis",'4') // DINAS LUAR
                        ->orderBy("tgl_in","DESC")
                        ->first();

        $cutiMap = [
            // 'DL'  => 'Dinas Luar',
            'L'  => 'Libur',
            'C'  => 'Cuti Tahunan',
            'CM' => 'Cuti Melahirkan',
            'CU' => 'Cuti Umroh',
            'CH' => 'Cuti Haji',
            'CD' => 'Cuti Diluar Tanggungan',
        ];

        if ($jarak > 30) {
            if ($jadwal) {
                if ($jadwal->progress == 3) {
                    $shift = ref_shift::leftJoin('referensi_jadwal_users', function($join) {
                                $join->on('referensi_jadwal_users.pegawai_id', '=', 'referensi_jadwal_shift.pegawai_id')
                                    ->whereNull('referensi_jadwal_users.deleted_at');
                            })
                            ->select('referensi_jadwal_shift.*')
                            ->whereRaw("
                                FIND_IN_SET(?,
                                    REPLACE(REPLACE(REPLACE(referensi_jadwal_users.staf, '\"', ''), '[', ''), ']', '')
                                )
                            ", [$jadwal->atasan])
                            ->where('referensi_jadwal_shift.singkat',$jadwal->$hit)
                            ->where('referensi_jadwal_shift.deleted_at',null)
                            ->first();

                    if ($shift) {
                        if (!$ijin) {
                            if (!$dinasLuar) {
                                if (!$show && !$showMalam) {
                                    $btn_ijin = true;
                                    if ($shift->berangkat > $shift->pulang) {
                                        $nama = 'Jadwal Shift Lewat Hari';
                                    } else {
                                        $nama = 'Jadwal Shift Reguler';
                                    }
                                    $jam = Carbon::parse($shift->berangkat)->isoFormat('HH:mm').' - '.Carbon::parse($shift->pulang)->isoFormat('HH:mm').' WIB';
                                    $keterangan = 'Shift '.$shift->shift;
                                    $message = 'Tidak diizinkan untuk melakukan Absensi. Anda hanya dapat mengajukan Ijin/Dinas Luar karena berada di luar radius Absensi (<30m dari titik lokasi).';
                                } else {
                                    if ($show && !$showMalam) {
                                        $nama = 'Jadwal Shift Reguler';
                                        $jam = Carbon::parse($show->ref_jam_masuk)->isoFormat('HH:mm').' - '.Carbon::parse($show->ref_jam_pulang)->isoFormat('HH:mm').' WIB';
                                        $keterangan = 'Shift '.$show->nm_shift;
                                        $jamPulangUntil = Carbon::parse($show->ref_jam_pulang)->addHour(2);
                                        if ($now->greaterThan($jamPulangUntil)) {
                                            $message = 'Waktu Absen Pulang telah terlewati (Hanya dari Pukul '.Carbon::parse($show->ref_jam_pulang)->isoFormat('HH:mm').' - '.$jamPulangUntil->isoFormat('HH:mm').' WIB) dengan Toleransi kepulangan +2jam.';
                                        } else {
                                            $message = 'Silakan kembali ke dalam radius Absensi (<30m dari titik lokasi). Absen Pulang Shift '.$show->nm_shift.' Anda adalah Pukul '.Carbon::parse($show->ref_jam_pulang)->isoFormat('HH:mm').' WIB.';
                                        }
                                    } else {
                                        if (!$show && $showMalam) {
                                            $nama = 'Jadwal Shift Lewat Hari';
                                            $jam = Carbon::parse($showMalam->ref_jam_masuk)->isoFormat('HH:mm').' - '.Carbon::parse($showMalam->ref_jam_pulang)->isoFormat('HH:mm').' WIB';
                                            $keterangan = 'Silakan kembali ke dalam radius Absensi (<30m dari titik lokasi). Absen Pulang Shift Malam ('.$showMalam->kd_shift.') Anda Hari ini adalah Pukul '.Carbon::parse($showMalam->ref_jam_pulang)->isoFormat('HH:mm').' WIB.';
                                        } else {
                                            // JIKA TERDETEKSI ADA ABSENSI DAN MASIH ADA TINGGALAN SHIFT MALAM (SHIFT BERIRISAN)
                                            $message = 'Mohon Maaf, waktu shift Anda beririsan antara jam berangkat dan jam pulang shift pada hari sebelumnya. Segera perbaiki data Referensi Shift dan Jadwal Dinas Anda.';
                                        }
                                    }
                                }
                            } else {
                                $nama = 'Absensi Hari Ini';
                                $jam = 'Dinas Luar';
                                $message = 'Abensi Hari ini telah terisi dengan Dinas Luar. Silakan absensi kembali pada hari/waktu Shift di hari selanjutnya. Terima Kasih.';
                            }
                        } else {
                            $nama = 'Absensi Hari Ini';
                            $jam = 'Ijin/Tidak Masuk';
                            $message = 'Abensi Hari ini telah terisi dengan Ijin. Silakan absensi kembali pada hari/waktu Shift selanjutnya. Terima Kasih.';
                        }
                    } else {
                        $nama = 'Hari ini ' . $cutiMap[$jadwal->$hit] ?? 'Libur / Tidak Masuk';
                        $jam = '-';
                        $keterangan = '';
                        $message = 'Absensi Hari ini adalah ' . $cutiMap[$jadwal->$hit] ?? 'Libur / Tidak Masuk';
                    }
                } else {
                    if ($jadwal->progress == 2) {
                        $nama = 'Jadwal Belum Divalidasi';
                        $jam = '';
                        $keterangan = '';
                        $message = 'Jadwal Dinas sudah diverifikasi oleh Atasan namun belum dilakukan validasi oleh Bagian SDI. Silakan Menunggu Proses Validasi. Terima Kasih.';
                    } else {
                        $nama = 'Jadwal Belum Diverifikasi';
                        $jam = '';
                        $keterangan = '';
                        $message = 'Jadwal Dinas belum diverifikasi oleh Atasan, silakan konfirmasi kepada Admin Jadwal Anda bulan ini. Terima Kasih.';
                    }
                }
            } else {
                $nama = 'Tidak Ada Jadwal';
                $jam = '';
                $keterangan = '';
                $message = 'Silakan menghubungi Admin Jadwal untuk melakukan Penambahan Jadwal Dinas Bulan ini bersama Bagian SDI. Terima Kasih.';
            }
        } else {
            if ($jadwal) {
                if ($jadwal->progress == 3) {
                    $shift = ref_shift::leftJoin('referensi_jadwal_users', function($join) {
                                $join->on('referensi_jadwal_users.pegawai_id', '=', 'referensi_jadwal_shift.pegawai_id')
                                    ->whereNull('referensi_jadwal_users.deleted_at');
                            })
                            ->select('referensi_jadwal_shift.*')
                            ->whereRaw("
                                FIND_IN_SET(?,
                                    REPLACE(REPLACE(REPLACE(referensi_jadwal_users.staf, '\"', ''), '[', ''), ']', '')
                                )
                            ", [$jadwal->atasan])
                            ->where('referensi_jadwal_shift.singkat',$jadwal->$hit)
                            ->where('referensi_jadwal_shift.deleted_at',null)
                            ->first();

                    if ($shift) {
                        if ($shift->berangkat > $shift->pulang) {
                            $nama = 'Jadwal Shift Lewat Hari';
                        } else {
                            $nama = 'Jadwal Shift Reguler';
                        }
                        $jam = Carbon::parse($shift->berangkat)->isoFormat('HH:mm').' - '.Carbon::parse($shift->pulang)->isoFormat('HH:mm').' WIB';
                        $keterangan = 'Shift '.$shift->shift;
                    } else {
                        $nama = 'Hari ini ' . $cutiMap[$jadwal->$hit] ?? 'Libur / Tidak Masuk';
                        $jam = '-';
                        $keterangan = '';
                    }

                    if ($shift) {
                        if (!$ijin) {
                            if (!$dinasLuar) {
                                $btn_ijin = true;
                                $jamMasuk = Carbon::parse($datenow . ' ' . $shift->berangkat)->subHour(2); // mulai dari 2 jam sebelum jam masuk
                                if ($shift->berangkat > $shift->pulang) {
                                    $jamPulang = Carbon::parse($datetommorow . ' ' . $shift->pulang);
                                } else {
                                    $jamPulang = Carbon::parse($datenow . ' ' . $shift->pulang);
                                }
                                $jamPulangUntil = Carbon::parse($datenow . ' ' . $shift->pulang)->addHour(2);
                                if (!$show) {
                                    if (!$showMalam) {
                                        if ($now->between($jamMasuk, $jamPulang)) {
                                            $btn_berangkat = true;
                                            $message = 'Silakan melakukan Absensi Masuk hari ini (Maksimal sebelum melewati Waktu Jam Pulang). Terima Kasih.';
                                        } elseif ($now->lessThan($jamMasuk)) {
                                            $message = 'Jam Absen Masuk saat ini TIDAK pada/antara jam masuk yang ditetapkan (yaitu -1 jam sebelum Referensi Jam Masuk) dan wajib tidak lebih dari jam pulang yang seharusnya.';
                                        } else {
                                            $message = 'Jam Absen Masuk telah terlewati. Absen Masuk TIDAK BOLEH melebihi Jam Pulang.';
                                        }
                                    } else {
                                        // JIKA MASIH ADA JAGA SHIFT YANG BELUM TERSELESAIKAN (KHUSUS LEWAT HARI)
                                        $jamPulangMalam = Carbon::parse($showMalam->ref_jam_pulang);
                                        $jamPulangMalamUntil = Carbon::parse($showMalam->ref_jam_pulang)->addHour(2);
                                        if ($now->greaterThan($jamPulangMalamUntil)) {
                                            if ($now->between($jamMasuk, $jamPulang)) {
                                                $btn_berangkat = true;
                                                $message = 'Silakan melakukan Absensi Masuk Shift '.$shift->shift.' hari ini (Maksimal sebelum melewati Waktu Jam Pulang Pukul '.Carbon::parse($jamPulang)->isoFormat('HH:mm').' WIB). Terima Kasih.';
                                            } elseif ($now->lessThan($jamMasuk)) {
                                                $message = 'Absen Masuk Shift '.$shift->shift.' Hari ini (Pukul '.$jamMasuk->isoFormat('HH:mm').' WIB) masih terkunci, Silakan menunggu.';
                                            } else {
                                                $message = 'Jam Absen Masuk Shift '.$shift->shift.' telah terlewati. Absen Masuk TIDAK BOLEH melebihi Jam Pulang.';
                                            }
                                        } else {
                                            $btn_ijin = false;
                                            if ($now->greaterThanOrEqualTo($jamPulangMalam) && $now->lessThan($jamPulangMalamUntil)) {
                                                $btn_pulang = true;
                                                $message = 'Silakan melakukan Absensi Pulang Shift Malam (Maksimal 2 Jam setelah Waktu Jam Pulang atau sampai dengan Pukul '.Carbon::parse($jamPulangMalamUntil)->isoFormat('HH:mm').' WIB). Terima Kasih.';
                                            } else { // $now->lessThan($jamPulangMalam)
                                                $message = 'Absen Pulang Shift Malam Anda Hari ini (Pukul '.$jamPulangMalam->isoFormat('HH:mm').' WIB) masih terkunci, Silakan menunggu.';
                                            }
                                            $nama = 'Jadwal Shift Lewat Hari';
                                            $jam = Carbon::parse($showMalam->ref_jam_masuk)->isoFormat('HH:mm').' - '.Carbon::parse($showMalam->ref_jam_pulang)->isoFormat('HH:mm').' WIB';
                                            $keterangan = 'Shift '.$showMalam->nm_shift;
                                        }
                                    }
                                } else {
                                    $btn_ijin = false;
                                    if ($show->tgl_in && !$show->tgl_out) { // BELUM PULANG
                                        if ($now->between($jamMasuk, $jamPulang)) {
                                            $message = 'Absen Pulang hari ini akan tersedia mulai Pukul '.Carbon::parse($jamPulang)->isoFormat('HH:mm').' - '.Carbon::parse($jamPulangUntil)->isoFormat('HH:mm').' WIB (Toleransi kepulangan +2jam).';
                                        } elseif ($now->greaterThanOrEqualTo($jamPulang) && $now->lessThan($jamPulangUntil)) {
                                            $btn_pulang = true;
                                            $message = 'Silakan melakukan Absen Pulang dari Pukul '.Carbon::parse($jamPulang)->isoFormat('HH:mm').' - '.Carbon::parse($jamPulangUntil)->isoFormat('HH:mm').' WIB (Toleransi kepulangan +2jam).';
                                        } else {
                                            $message = 'Absen Pulang telah terlewati (Hanya dari Pukul '.Carbon::parse($jamPulang)->isoFormat('HH:mm').' - '.Carbon::parse($jamPulangUntil)->isoFormat('HH:mm').' WIB) dengan Toleransi kepulangan +2jam.';
                                        }
                                    } else { // SUDAH PULANG
                                        $message = 'Absensi Masuk dan Pulang Hari ini telah selesai dilakukan. Silakan Absensi kembali pada hari selanjutnya. Terima Kasih.';
                                    }
                                }
                            } else {
                                $message = 'Absensi Hari ini telah terisi dengan Dinas Luar. Silakan melakukan Absensi kembali pada hari selanjutnya. Terima Kasih.';
                            }
                        } else {
                            $message = 'Absensi Hari ini telah terisi dengan Ijin. Silakan melakukan Absensi kembali pada hari selanjutnya. Terima Kasih.';
                        }
                    } else { // JIKA HARI INI TIDAK ADA MASUK SHIFT
                        if (!$showMalam) {
                            $message = 'Hari ini Anda sedang '.$cutiMap[$jadwal->$hit].', Absensi tidak diizinkan.' ?? 'Selamat berlibur hari ini dan beraktivitas kembali di kemudian hari.';
                        } else {
                            $jamPulangMalam = Carbon::parse($showMalam->ref_jam_pulang);
                            $jamPulangMalamUntil = Carbon::parse($showMalam->ref_jam_pulang)->addHour(2);
                            if ($now->greaterThan($jamPulangMalamUntil)) {
                                $message = 'Hari ini Anda sedang '.$cutiMap[$jadwal->$hit].', Absensi tidak diizinkan.' ?? 'Selamat berlibur hari ini dan beraktivitas kembali di kemudian hari.';
                            } else {
                                if ($now->greaterThanOrEqualTo($jamPulangMalam) && $now->lessThan($jamPulangMalamUntil)) {
                                    $btn_pulang = true;
                                    $message = 'Silakan melakukan Absensi Pulang Shift Malam (Maksimal 2 Jam setelah Waktu Jam Pulang atau sampai dengan Pukul '.Carbon::parse($jamPulangMalamUntil)->isoFormat('HH:mm').' WIB). Terima Kasih.';
                                } else { // $now->lessThan($jamPulangMalam)
                                    $message = 'Absen Pulang Shift Malam Anda Hari ini (Pukul '.$jamPulangMalam->isoFormat('HH:mm').' WIB) masih terkunci, Silakan menunggu.';
                                }
                                $nama = 'Jadwal Shift Lewat Hari';
                                $jam = Carbon::parse($showMalam->ref_jam_masuk)->isoFormat('HH:mm').' - '.Carbon::parse($showMalam->ref_jam_pulang)->isoFormat('HH:mm').' WIB';
                                $keterangan = 'Shift '.$showMalam->nm_shift;
                            }
                        }
                    }
                } else {
                    if ($jadwal->progress == 2) {
                        $nama = 'Jadwal Belum Divalidasi';
                        $jam = '';
                        $keterangan = '';
                        $message = 'Jadwal Dinas sudah diverifikasi oleh Atasan namun belum dilakukan validasi oleh Bagian SDI. Silakan Menunggu Proses Validasi. Terima Kasih.';
                    } else {
                        $nama = 'Jadwal Belum Diverifikasi';
                        $jam = '';
                        $keterangan = '';
                        $message = 'Jadwal Dinas belum diverifikasi oleh Atasan, silakan konfirmasi kepada Admin Jadwal Anda bulan ini. Terima Kasih.';
                    }
                }
            } else {
                $nama = 'Tidak Ada Jadwal';
                $jam = '';
                $keterangan = '';
                $message = 'Silakan menghubungi Admin Jadwal untuk melakukan Penambahan Jadwal Dinas Bulan ini bersama Bagian SDI. Terima Kasih.';
            }
        }

        // print_r($btn_pulang);
        // die();
        return response()->json([
            'pulang' => $btn_pulang,
            'berangkat' => $btn_berangkat,
            'ijin' => $btn_ijin,
            'nama' => $nama,
            'jam' => $jam,
            'keterangan' => $keterangan,
            'message' => $message,
        ]);
    }

    function absensi(Request $request)
    {
        $now = Carbon::now('Asia/Jakarta');
        $time = $now->isoFormat('HH:mm:ss'); // Format jam 24-jam
        $datenow = $now->toDateString();
        $today = $now->isoFormat('YYYY-MM-DD');
        $yesterday = $now->copy()->subDay()->toDateString();
        $tommorow = $now->copy()->addDay()->toDateString();
        $tahun = $now->format('Y');
        $bulan = $now->format('m');
        $tgl = $now->format('j');
        $hit = "tgl".$tgl;
        $user = $request->id_user;
        $jenis = $request->jenis;
        // JENIS = 1 = BERANGKAT
        // JENIS = 2 = PULANG
        // JENIS = 3 = IJIN
        // JENIS = 4 = DINAS LUAR

        if ($jenis == 1) { // ABSENSI MASUK/BERANGKAT
            $jadwal = jadwal_detail::leftJoin('kepegawaian_jadwal', function($join) {
                                $join->on('kepegawaian_jadwal.id', '=', 'kepegawaian_jadwal_detail.id_jadwal')
                                    ->whereNull('kepegawaian_jadwal.deleted_at');
                            })
                            ->select('kepegawaian_jadwal.pegawai_id as id_atasan','kepegawaian_jadwal.staf','kepegawaian_jadwal.bulan','kepegawaian_jadwal.tahun','kepegawaian_jadwal_detail.*')
                            // ->whereJsonContains('kepegawaian_jadwal.staf', $user)
                            ->where('kepegawaian_jadwal_detail.pegawai_id',$user)
                            ->where('kepegawaian_jadwal.bulan',$bulan)
                            ->where('kepegawaian_jadwal.tahun',$tahun)
                            ->whereNull('kepegawaian_jadwal_detail.deleted_at')
                            ->orderBy('kepegawaian_jadwal_detail.id','DESC')
                            ->first();

            // EXECUTE
            $callShift = $jadwal->$hit;
            if ($callShift) {
                // FIND SHIFT
                $shift = ref_shift::leftJoin('referensi_jadwal_users', function($join) {
                            $join->on('referensi_jadwal_users.pegawai_id', '=', 'referensi_jadwal_shift.pegawai_id')
                                ->whereNull('referensi_jadwal_users.deleted_at');
                        })
                        ->select('referensi_jadwal_shift.*')
                        ->whereRaw("
                            FIND_IN_SET(?,
                                REPLACE(REPLACE(REPLACE(referensi_jadwal_users.staf, '\"', ''), '[', ''), ']', '')
                            )
                        ", [$jadwal->id_atasan])
                        ->where('referensi_jadwal_shift.singkat',$callShift)
                        ->where('referensi_jadwal_shift.deleted_at',null)
                        ->first();

                if ($shift) {
                    $berangkat = null;
                    $pulang = null;
                    $jam_masuk = Carbon::parse($shift->berangkat);
                    $jam_pulang = Carbon::parse($shift->pulang);

                    if ($jam_pulang->greaterThan($jam_masuk)) {
                        $lewat_hari = 0; // TIDAK LEWAT HARI
                    } else {
                        $lewat_hari = 1; // LEWAT HARI / MALAM
                    }

                    // VALIDATING JAM MASUK
                    if ($jam_pulang->greaterThan($jam_masuk)) { // KECUALI MALAM ATAU LEWAT HARI
                        if ($time >= Carbon::parse($shift->berangkat)->subHour(2)->isoFormat('HH:mm:ss') && $time <= Carbon::parse($shift->pulang)->isoFormat('HH:mm:ss')) { // DALAM JAM KERJA (MIN 2 JAM SEBELUM JAM MASUK)
                            $berangkat = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $shift->berangkat, 'Asia/Jakarta')->format('Y-m-d H:i:s');
                            $pulang = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $shift->pulang, 'Asia/Jakarta')->format('Y-m-d H:i:s');
                        } else {
                            return Response::json(array(
                                'message' => 'Absen Masuk belum tersedia!',
                                'code' => 401,
                            ));
                        }
                    } else { // KHUSUS JAGA LEWAT HARI (SHIFT MALAM)
                        $convBerangkat = Carbon::parse($today.' '.$shift->berangkat)->subHour(2); // MULAI ABSENSI MINIMAL 2 JAM SEBELUM JAM MASUK
                        $convPulang = Carbon::parse($tommorow.' '.$shift->pulang);
                        if ($now >= $convBerangkat && $now <= $convPulang) { // DALAM JAM KERJA
                            $berangkat = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $shift->berangkat, 'Asia/Jakarta')->format('Y-m-d H:i:s');
                            $pulang = Carbon::createFromFormat('Y-m-d H:i:s', $tommorow . ' ' . $shift->pulang, 'Asia/Jakarta')->format('Y-m-d H:i:s');
                        } else {
                            return Response::json(array(
                                'message' => 'Absen Masuk belum tersedia!',
                                'code' => 401,
                            ));
                        }
                    }

                    // INITIALIZE PROCESS = ABSENSI
                    if ($request->hasFile('foto')) {
                        // PERHITUNGAN SELISIH JAM SAAT MASUK SAMPAI KETERLAMBATAN
                        $initBerangkat = Carbon::parse($berangkat)->addMinutes(10);
                        $harusnyaBerangkat = new Carbon($initBerangkat); // ->isoFormat('YYYY-MM-DD H:mm:ss')
                        if ($now->gt($harusnyaBerangkat)) {
                            $diff = $now->diff($harusnyaBerangkat)->format('%H:%I:%S');
                            $terlambat = 1; // TERLAMBAT
                            $nm_terlambat = 'Terlambat';
                        } else {
                            $diff = Carbon::parse('00:00:00')->isoFormat('HH:mm:ss');
                            $terlambat = 0; // DISIPLIN
                            $nm_terlambat = 'Disiplin';
                        }

                        if (Carbon::createFromFormat('Y-m-d H:i:s', $berangkat, 'Asia/Jakarta')->isToday()) {
                            $validasi = absensi::where('pegawai_id',$user)
                                            ->where('jenis',1)
                                            ->where('kd_shift',$shift->singkat)
                                            ->where('ref_jam_masuk',$berangkat)
                                            ->where('ref_jam_pulang',$pulang)
                                            ->get();

                            if ($validasi->isNotEmpty()) {
                                return Response::json(array(
                                    'message' => 'Absen Masuk sudah tercatat! Silakan melanjutkan Aktifitas Bekerja Anda!',
                                    'code' => 401,
                                ));
                            }

                            // SAVE FOTO
                            $file = $request->file('foto');
                            $title = uniqid() . '.' . $file->getClientOriginalExtension();
                            $path = $file->storeAs('public/files/kepegawaian/absensi/masuk', $title);

                            // SAVE DB
                            $data = new absensi;
                            $data->jenis = 1;
                            $data->pegawai_id = $user;
                            $data->kd_shift = $shift->singkat;
                            $data->nm_shift = $shift->shift;
                            $data->ref_jam_masuk = $berangkat;
                            $data->ref_jam_pulang = $pulang;
                            $data->keterlambatan = $diff;
                            $data->tgl_in = Carbon::now('Asia/Jakarta');
                            $data->foto_in = $title;
                            // $data->title_in = $path;
                            $data->path_in = $path;
                            $data->lokasi_in = $request->latitude.', '.$request->longitude;
                            $data->terlambat = $terlambat;
                            $data->lewat_hari = $lewat_hari;
                            $data->save();

                            return Response::json(array(
                                'message' => 'Absen Masuk Jaga '.$shift->shift.' berhasil pada '.Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y \P\u\k\u\l H.i').' WIB dan tercatat '.$nm_terlambat.', selamat beraktifitas. Semangat!! :)',
                                'code' => 200,
                            ));
                        } else {
                            return Response::json(array(
                                'message' => 'Jam Berangkat yang terkirim ke Sistem TIDAK VALID, bisa jadi dikarenakan banyaknya Cache yang menumpuk pada Device Anda. Silakan Refresh halaman Absensi ini dan lakukan sekali lagi. Terima Kasih.',
                                'code' => 401,
                            ));
                        }
                    } else {
                        return Response::json(array(
                            'message' => 'Hasil selfi kamera tidak ditemukan, pastikan kamera Anda dalam kondisi Normal. Apabila masih belum dapat melakukan Absensi, silakan menghubungi Admin. Terima Kasih.',
                            'code' => 401,
                        ));
                    }
                } else {
                    return Response::json(array(
                        'message' => 'Shift tidak valid. Pastikan Hari ini Anda masuk jaga Shift atau Libur. Silakan konfirmasi kepada Admin Jadwal bulan ini untuk memastikan Nama Referensi Shift sudah benar dan Valid!',
                        'code' => 401,
                    ));
                }
            } else {
                return Response::json(array(
                    'message' => 'Jadwal tidak valid. Konfirmasi dengan Admin Jadwal Dinas Anda!',
                    'code' => 401,
                ));
            }
        } else {
            if ($jenis == 2) { // ABSENSI PULANG
                if ($request->hasFile('foto')) {
                    $file = $request->file('foto');
                    $title = uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('public/files/kepegawaian/absensi/pulang', $title);

                    // VALIDASI DUPLIKASI DATA
                    $validate = absensi::where('pegawai_id',$user)
                                ->whereDate("ref_jam_pulang","=",$datenow)
                                ->whereNull('deleted_at')
                                ->orderBy('updated_at','DESC')
                                ->get();

                    if ($validate->count() > 1) {
                        // Ambil data pertama dan buang sisanya
                        $validate->skip(1)->each->delete();
                        // $validate->slice(1)->each->delete(); // Lebih Berat dari skip()
                    }

                    // GET DATA TO UPDATE
                    // Ambil data pertama dari hasil yang sudah diambil
                    $data = $validate->first();
                    if (!$data) {
                        return Response::json([
                            'message' => 'Data absensi tidak ditemukan. Segera hubungi Developer!',
                            'code' => 404,
                        ]);
                    }

                    // PERHITUNGAN LEMBUR JAM PULANG
                    $jam_pulang_seharusnya = new Carbon($data->ref_jam_pulang); // ->isoFormat('YYYY-MM-DD H:mm:ss')
                    $jam_pulang_sekarang = $now;
                    $diffLembur = $jam_pulang_seharusnya->diff($jam_pulang_sekarang)->format('%H:%I:%S');

                    // PERHITUNGAN SELISIH JAM SAAT MASUK SAMPAI PULANG
                    $jam_berangkat = new Carbon($data->tgl_in); // ->isoFormat('YYYY-MM-DD H:mm:ss')
                    $jam_pulang = $now;
                    $diffKerja = $jam_berangkat->lessThan($jam_pulang)
                                ? $jam_berangkat->diff($jam_pulang)->format('%H:%I:%S')
                                : '00:00:00';

                    // $data->ip_out = $this->getClientIp();
                    $data->tgl_out = $now;
                    $data->lembur = $diffLembur;
                    $data->selisih_jam = $diffKerja;
                    $data->foto_out = $title;
                    $data->path_out = $path;
                    $data->lokasi_out = $request->latitude.', '.$request->longitude;
                    $data->save();

                    return Response::json(array(
                        'message' => 'Absen Pulang Jaga '.$data->nm_shift.' telah berhasil, hati-hati di jalan.',
                        'code' => 200,
                    ));
                } else {
                    return Response::json(array(
                        'message' => 'Hasil selfi kamera tidak ditemukan, pastikan kamera Anda dalam kondisi Normal. Apabila masih belum dapat melakukan Absensi, silakan menghubungi Admin. Terima Kasih.',
                        'code' => 400,
                    ));
                }
            } else {
                if ($jenis == 3) { // ABSENSI IJIN
                    if (!$request->keterangan) {
                        return Response::json(array(
                            'message' => 'Keterangan Wajib Diisi. Silakan mengulangi Pengajuan Ijin kembali dengan mengisi Keterangan Ijin. Terima Kasih.',
                            'code' => 401,
                        ));
                    }

                    $jadwal = jadwal_detail::leftJoin('kepegawaian_jadwal', function($join) {
                                        $join->on('kepegawaian_jadwal.id', '=', 'kepegawaian_jadwal_detail.id_jadwal')
                                            ->whereNull('kepegawaian_jadwal.deleted_at');
                                    })
                                    ->select('kepegawaian_jadwal.pegawai_id as id_atasan','kepegawaian_jadwal.staf','kepegawaian_jadwal.bulan','kepegawaian_jadwal.tahun','kepegawaian_jadwal_detail.*')
                                    // ->whereJsonContains('kepegawaian_jadwal.staf', $user)
                                    ->where('kepegawaian_jadwal_detail.pegawai_id',$user)
                                    ->where('kepegawaian_jadwal.bulan',$bulan)
                                    ->where('kepegawaian_jadwal.tahun',$tahun)
                                    ->whereNull('kepegawaian_jadwal_detail.deleted_at')
                                    ->orderBy('kepegawaian_jadwal_detail.id','DESC')
                                    ->first();

                    // EXECUTE
                    $callShift = $jadwal->$hit;

                    if ($callShift) {
                        // FIND SHIFT
                        $shift = ref_shift::leftJoin('referensi_jadwal_users', function($join) {
                                    $join->on('referensi_jadwal_users.pegawai_id', '=', 'referensi_jadwal_shift.pegawai_id')
                                        ->whereNull('referensi_jadwal_users.deleted_at');
                                })
                                ->select('referensi_jadwal_shift.*')
                                ->whereRaw("
                                    FIND_IN_SET(?,
                                        REPLACE(REPLACE(REPLACE(referensi_jadwal_users.staf, '\"', ''), '[', ''), ']', '')
                                    )
                                ", [$jadwal->id_atasan])
                                ->where('referensi_jadwal_shift.singkat',$callShift)
                                ->where('referensi_jadwal_shift.deleted_at',null)
                                ->first();

                        if ($shift) {
                            if ($request->hasFile('foto')) {
                                $file = $request->file('foto');
                                $title = uniqid() . '.' . $file->getClientOriginalExtension();
                                $path = $file->storeAs('public/files/kepegawaian/absensi/ijin', $title);

                                $jamMasuk = Carbon::parse($shift->berangkat)->format('Y-m-d H:i:s');
                                $jamPulang = Carbon::parse($shift->pulang)->format('Y-m-d H:i:s');

                                $data = new absensi;
                                $data->jenis = 3;
                                // $data->ip_in = $this->getClientIp();
                                // $data->user_agent = $userAgent;
                                $data->pegawai_id = $user;
                                $data->kd_shift = $shift->singkat;
                                $data->nm_shift = $shift->shift;
                                $data->ref_jam_masuk = $jamMasuk;
                                $data->ref_jam_pulang = $jamPulang;
                                $data->keterlambatan = null;
                                $data->lembur = null;
                                $data->tgl_in = Carbon::now('Asia/Jakarta');
                                $data->tgl_out = null;
                                $data->selisih_jam = null;
                                $data->foto_in = $title;
                                $data->path_in = $path;
                                $data->foto_out = null;
                                $data->path_out = null;
                                $data->lokasi_in = $request->latitude.', '.$request->longitude;
                                $data->lokasi_out = null;
                                $data->terlambat = null;
                                $data->keterangan = $request->keterangan ? $request->keterangan : '';
                                $data->lewat_hari = 0;
                                $data->save();

                                return Response::json(array(
                                    'message' => 'Surat Ijin berhasil dikirimkan. Silakan melanjutkan aktivitas Anda.',
                                    'code' => 200,
                                ));
                            } else {
                                return Response::json(array(
                                    'message' => 'Hasil selfi kamera tidak ditemukan, pastikan kamera Anda dalam kondisi Normal. Apabila masih belum dapat melakukan Absensi, silakan menghubungi Admin. Terima Kasih.',
                                    'code' => 401,
                                ));
                            }
                        } else {
                            return Response::json(array(
                                'message' => 'Shift tidak valid. Pastikan Hari ini Anda masuk jaga Shift atau Libur. Silakan konfirmasi kepada Admin Jadwal bulan ini untuk memastikan Nama Referensi Shift sudah benar dan Valid!',
                                'code' => 401,
                            ));
                        }
                    } else {
                        return Response::json(array(
                            'message' => 'Jadwal tidak valid. Konfirmasi dengan Admin Jadwal Dinas Anda!',
                            'code' => 401,
                        ));
                    }
                } else {
                    if ($jenis == 4) {
                        if (!$request->keterangan) {
                            return Response::json(array(
                                'message' => 'Keterangan Wajib Diisi. Silakan mengulangi Pengajuan Dinas Luar kembali dengan mengisi Keterangan yang tersedia. Terima Kasih.',
                                'code' => 401,
                            ));
                        }

                        $jadwal = jadwal_detail::leftJoin('kepegawaian_jadwal', function($join) {
                                            $join->on('kepegawaian_jadwal.id', '=', 'kepegawaian_jadwal_detail.id_jadwal')
                                                ->whereNull('kepegawaian_jadwal.deleted_at');
                                        })
                                        ->select('kepegawaian_jadwal.pegawai_id as id_atasan','kepegawaian_jadwal.staf','kepegawaian_jadwal.bulan','kepegawaian_jadwal.tahun','kepegawaian_jadwal_detail.*')
                                        // ->whereJsonContains('kepegawaian_jadwal.staf', $user)
                                        ->where('kepegawaian_jadwal_detail.pegawai_id',$user)
                                        ->where('kepegawaian_jadwal.bulan',$bulan)
                                        ->where('kepegawaian_jadwal.tahun',$tahun)
                                        ->whereNull('kepegawaian_jadwal_detail.deleted_at')
                                        ->orderBy('kepegawaian_jadwal_detail.id','DESC')
                                        ->first();

                        // EXECUTE
                        $callShift = $jadwal->$hit;

                        if ($callShift) {
                            // FIND SHIFT
                            $shift = ref_shift::leftJoin('referensi_jadwal_users', function($join) {
                                        $join->on('referensi_jadwal_users.pegawai_id', '=', 'referensi_jadwal_shift.pegawai_id')
                                            ->whereNull('referensi_jadwal_users.deleted_at');
                                    })
                                    ->select('referensi_jadwal_shift.*')
                                    ->whereRaw("
                                        FIND_IN_SET(?,
                                            REPLACE(REPLACE(REPLACE(referensi_jadwal_users.staf, '\"', ''), '[', ''), ']', '')
                                        )
                                    ", [$jadwal->id_atasan])
                                    ->where('referensi_jadwal_shift.singkat',$callShift)
                                    ->where('referensi_jadwal_shift.deleted_at',null)
                                    ->first();

                            if ($shift) {
                                if ($request->hasFile('foto')) {
                                    $file = $request->file('foto');
                                    $title = uniqid() . '.' . $file->getClientOriginalExtension();
                                    $path = $file->storeAs('public/files/kepegawaian/absensi/dinasluar', $title);

                                    $jamMasuk = Carbon::parse($shift->berangkat)->format('Y-m-d H:i:s');
                                    $jamPulang = Carbon::parse($shift->pulang)->format('Y-m-d H:i:s');

                                    $data = new absensi;
                                    $data->jenis = 4;
                                    // $data->ip_in = $this->getClientIp();
                                    // $data->user_agent = $userAgent;
                                    $data->pegawai_id = $user;
                                    $data->kd_shift = $shift->singkat;
                                    $data->nm_shift = $shift->shift;
                                    $data->ref_jam_masuk = $jamMasuk;
                                    $data->ref_jam_pulang = $jamPulang;
                                    $data->keterlambatan = null;
                                    $data->lembur = null;
                                    $data->tgl_in = Carbon::now('Asia/Jakarta');
                                    $data->tgl_out = null;
                                    $data->selisih_jam = null;
                                    $data->foto_in = $title;
                                    $data->path_in = $path;
                                    $data->foto_out = null;
                                    $data->path_out = null;
                                    $data->lokasi_in = $request->latitude.', '.$request->longitude;
                                    $data->lokasi_out = null;
                                    $data->terlambat = null;
                                    $data->keterangan = $request->keterangan ? $request->keterangan : '';
                                    $data->lewat_hari = 0;
                                    $data->save();

                                    return Response::json(array(
                                        'message' => 'Pengajuan Dinas Luar berhasil dikirimkan. Silakan melanjutkan aktivitas Dinas Anda.',
                                        'code' => 200,
                                    ));
                                } else {
                                    return Response::json(array(
                                        'message' => 'Hasil selfi kamera tidak ditemukan, pastikan kamera Anda dalam kondisi Normal. Apabila masih belum dapat melakukan Absensi, silakan menghubungi Admin. Terima Kasih.',
                                        'code' => 401,
                                    ));
                                }
                            } else {
                                return Response::json(array(
                                    'message' => 'Shift tidak valid. Pastikan Hari ini Anda masuk jaga Shift atau Libur. Silakan konfirmasi kepada Admin Jadwal bulan ini untuk memastikan Nama Referensi Shift sudah benar dan Valid!',
                                    'code' => 401,
                                ));
                            }
                        } else {
                            return Response::json(array(
                                'message' => 'Jadwal tidak valid. Konfirmasi dengan Admin Jadwal Dinas Anda!',
                                'code' => 401,
                            ));
                        }
                    } else {
                        return Response::json(array(
                            'message' => 'Absensi Tidak Valid. Mohon segera menghubungi Tim Developer!',
                            'code' => 401,
                        ));
                    }
                }

            }

        }
    }

    function detailAbsensi($id)
    {
        $show = absensi::where('id',$id)
                        // ->where('pegawai_id',$user)
                        ->first();

        // if ($show->jenis == 1) {
        //     $jenis = 'Masuk Shift';
        // } else {
        //     $jenis = 'Cuti/Tidak Masuk';
        // }

        if ($show->terlambat == 1) {
            $status_terlambat = "Terlambat";
        } else {
            $status_terlambat = "Tepat Waktu";
        }

        if ($show->tgl_out) {
            $tgl_out = Carbon::parse($show->tgl_out)->translatedFormat('l, j F Y');
            $jam_out = Carbon::parse($show->tgl_out)->format('H.i');
            if ($show->keterlambatan == '00:00:00') {
                $terlambat = '-';
            } else {
                $terlambat = $this->formatDurasiWaktu($show->keterlambatan);
            }
            $total_kerja = $this->formatDurasiWaktu($show->selisih_jam);
            $lembur = $this->formatDurasiWaktu($show->lembur);
        } else {
            $tgl_out = null;
            $jam_out = null;
            $terlambat = null;
            $total_kerja = null;
            $lembur = null;
        }

        return Response::json(array(
            "id" => $show->id,
            "jenis" => $show->jenis,
            "shift" => $show->nm_shift.' ('.$show->kd_shift.')',
            "status" => $status_terlambat,
            "tgl_in" => Carbon::parse($show->tgl_in)->translatedFormat('l, j F Y'),
            "jam_in" => Carbon::parse($show->tgl_in)->format('H.i'),
            "latlong_in" => $show->lokasi_in ?? "-7.637823555197155, 110.86796229092549",
            "latlong_out" => $show->lokasi_out ?? null,
            "terlambat" => $terlambat,
            "tgl_out" => $tgl_out,
            "jam_out" => $jam_out,
            "foto_in" => "https://absensi.simrsmu.com/api/kepegawaian/detail/foto/".$show->id."/1",
            "foto_out" => "https://absensi.simrsmu.com/api/kepegawaian/detail/foto/".$show->id."/0",
            "durasi_kerja" => $total_kerja,
            "lembur" => $lembur,
            "keterangan" => $show->keterangan ?? "Tidak ada.",
            "code" => 200
        ));
    }

    public function distance($lat1, $lon1, $lat2, $lon2) // Menghitung Jarak
    {
        // lat1 = latitude kantor
        // lon1 = longitude kantor
        // lat2 = latitude user
        // lon2 = longitude user

        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }

    function formatDurasiWaktu($time)
    {
        $carbon = Carbon::createFromFormat('H:i:s', $time);
        return "{$carbon->hour} jam {$carbon->minute} menit {$carbon->second} detik";
    }
}
