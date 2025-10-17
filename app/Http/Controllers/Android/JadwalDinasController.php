<?php

namespace App\Http\Controllers\Android;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\users;
use App\Models\jadwal;
use App\Models\jadwal_detail;
use App\Models\ref_shift;
use App\Models\ref_jabatan;
use Jenssegers\Agent\Agent;
use Carbon\Carbon;
use Auth,Validator,Redirect,Response,File,Storage;

class JadwalDinasController extends Controller
{
    function index($user, $bulan, $tahun)
    {
        $jadwalArray = [];
        $rekanKerjaArray = [];
        $shiftArray = [];
        $iconArray = [];
        $colorArray = [];
        $flowArray = [];
        $refJam = [];

        $jadwal = jadwal_detail::leftJoin('kepegawaian_jadwal', function($join) {
                            $join->on('kepegawaian_jadwal.id', '=', 'kepegawaian_jadwal_detail.id_jadwal')
                                ->whereNull('kepegawaian_jadwal.deleted_at');
                        })
                        ->leftJoin('users as uad','uad.id','=','kepegawaian_jadwal.pegawai_id')
                        ->leftJoin('users as uve','uve.id','=','kepegawaian_jadwal.verif')
                        ->leftJoin('users as uva','uva.id','=','kepegawaian_jadwal.valid')
                        ->select(
                            'kepegawaian_jadwal_detail.*','uad.nama as nama_admin','kepegawaian_jadwal.staf as bawahan','kepegawaian_jadwal.progress',
                            'uve.nama as nama_verif','kepegawaian_jadwal.tgl_verif','uva.nama as nama_valid','kepegawaian_jadwal.tgl_valid','kepegawaian_jadwal.created_at as tgl_dibuat'
                        )
                        ->where('kepegawaian_jadwal_detail.pegawai_id',$user)
                        ->where('kepegawaian_jadwal.bulan',$bulan)
                        ->where('kepegawaian_jadwal.tahun',$tahun)
                        ->whereNull('kepegawaian_jadwal_detail.deleted_at')
                        ->orderBy('kepegawaian_jadwal_detail.id','DESC')
                        ->first();

        $shift  = ref_shift::leftJoin('referensi_jadwal_users', function($join) {
                    $join->on('referensi_jadwal_users.pegawai_id', '=', 'referensi_jadwal_shift.pegawai_id')
                        ->whereNull('referensi_jadwal_users.deleted_at');
                })
                ->select('referensi_jadwal_shift.*')
                ->whereJsonContains('referensi_jadwal_users.staf', $user)
                ->where('referensi_jadwal_shift.deleted_at',null)
                ->get();

        $jabatan = ref_jabatan::leftJoin('referensi_jadwal_users', function($join) {
                    $join->on('referensi_jadwal_users.pegawai_id', '=', 'referensi_jadwal_users_jabatan.pegawai_id')
                        ->whereNull('referensi_jadwal_users.deleted_at');
                })
                ->select('referensi_jadwal_users_jabatan.*')
                ->whereJsonContains('referensi_jadwal_users.staf', $user)
                ->whereNull('referensi_jadwal_users_jabatan.deleted_at')
                ->get();

        $users = users::select('id', 'nama', 'name')
            ->whereNull('deleted_at')
            ->where('status', null)
            ->get();

        if ($jadwal) {
            $attributes = $jadwal->getAttributes();
            for ($i = 1; $i <= 31; $i++) {
                $key = str_pad($i, 2, '0', STR_PAD_LEFT);
                $field = 'tgl' . $i;
                $jadwalArray[$key] = $attributes[$field] ?? "";
            }
        }

        if (!empty($shift)) {
            // PUSH SHIFT & COLOR
            foreach ($shift as $key => $value) {
                $shiftArray[$value->singkat] = $value->shift ?? "";
                $iconArray[$value->singkat] = "check_mark_circled_solid";
                if (Carbon::parse($value->pulang) > Carbon::parse($value->berangkat)) {
                    $colorArray[$value->singkat] = "activeGreen";
                } else { // LEWAT HARI
                    $colorArray[$value->singkat] = "activeBlue";
                }
            }
            $shiftArray["L"] = "Libur";
            $shiftArray["C"] = "Cuti Tahunan";
            $shiftArray["CM"] = "Cuti Melahirkan";
            $shiftArray["CD"] = "Cuti Diluar Tanggungan";
            $shiftArray["CU"] = "Cuti Umroh";
            $shiftArray["CH"] = "Cuti Haji";

            $iconArray["L"] = "check_mark_circled";
            $iconArray["C"] = "minus_circle_fill";
            $iconArray["CM"] = "minus_circle_fill";
            $iconArray["CD"] = "clear_circled";
            $iconArray["CU"] = "minus_circle_fill";
            $iconArray["CH"] = "minus_circle_fill";

            $colorArray["L"] = "systemGrey2";
            $colorArray["C"] = "systemOrange";
            $colorArray["CM"] = "systemPink";
            $colorArray["CD"] = "systemRed";
            $colorArray["CU"] = "systemTeal";
            $colorArray["CH"] = "systemIndigo";
        }

        // 1. Buat map user by ID
        $usersMap = [];
        foreach ($users as $user) {
            $usersMap[$user->id] = $user;
        }

        // 2. Buat map jabatan by id_staf
        $jabatanMap = [];
        foreach ($jabatan as $jab) {
            $jabatanMap[$jab->id_staf] = $jab;
        }

        // 3. Loop bawahan + build staf
        $staf = [];
        foreach (json_decode($jadwal->bawahan) as $id) {
            if (isset($usersMap[$id])) {
                $user = $usersMap[$id];
                if (isset($jabatanMap[$id])) {
                    $jab = $jabatanMap[$id];
                    $staf[] = $user->nama . ' (' . $jab->jabatan . ')';
                } else {
                    $staf[] = $user->nama ?? $user->name;
                }
            }
        }

        return response()->json([
            "jadwal" => $jadwalArray,
            "rekan_kerja" => $rekanKerjaArray ?? [],
            "ref_shift" => $shiftArray,
            "ref_jam" => $refJam ?? [],
            "icon" => $iconArray,
            "color" => $colorArray,
            "flow" => [
                "Admin Jadwal" => $jadwal->nama_admin ?? "",
                "Tgl Dibuat" => $jadwal->tgl_dibuat ? $this->convertTgl($jadwal->tgl_dibuat) : "",
                "Verifikator" => $jadwal->nama_verif ?? "",
                "Tgl Diverifikasi" => $jadwal->tgl_verif ? $this->convertTgl($jadwal->tgl_verif) : "",
                "Validator" => $jadwal->nama_valid ?? "",
                "Tgl Validasi" => $jadwal->tgl_valid ? $this->convertTgl($jadwal->tgl_valid) : "",
                "Daftar Staf" => $staf,
            ],
        ]);
    }

    function index2($user, $bulan, $tahun)
    {
        $jadwalArray = [];
        $rekanKerjaArray = [];
        $shiftArray = [];
        $iconArray = [];
        $colorArray = [];
        $flowArray = [];
        $refJam = [];

        $jadwal = jadwal_detail::leftJoin('kepegawaian_jadwal', function($join) {
                            $join->on('kepegawaian_jadwal.id', '=', 'kepegawaian_jadwal_detail.id_jadwal')
                                ->whereNull('kepegawaian_jadwal.deleted_at');
                        })
                        ->leftJoin('users as uad','uad.id','=','kepegawaian_jadwal.pegawai_id')
                        ->leftJoin('users as uve','uve.id','=','kepegawaian_jadwal.verif')
                        ->leftJoin('users as uva','uva.id','=','kepegawaian_jadwal.valid')
                        ->select(
                            'kepegawaian_jadwal_detail.*','uad.nama as nama_admin','kepegawaian_jadwal_detail.id_jadwal','kepegawaian_jadwal.staf as bawahan','kepegawaian_jadwal.progress',
                            'uve.nama as nama_verif','kepegawaian_jadwal.tgl_verif','uva.nama as nama_valid','kepegawaian_jadwal.tgl_valid','kepegawaian_jadwal.created_at as tgl_dibuat'
                        )
                        ->where('kepegawaian_jadwal_detail.pegawai_id',$user)
                        ->where('kepegawaian_jadwal.bulan',$bulan)
                        ->where('kepegawaian_jadwal.tahun',$tahun)
                        ->whereNull('kepegawaian_jadwal_detail.deleted_at')
                        ->orderBy('kepegawaian_jadwal_detail.id','DESC')
                        ->first();

        $shift  = ref_shift::leftJoin('referensi_jadwal_users', function($join) {
                    $join->on('referensi_jadwal_users.pegawai_id', '=', 'referensi_jadwal_shift.pegawai_id')
                        ->whereNull('referensi_jadwal_users.deleted_at');
                })
                ->select('referensi_jadwal_shift.*')
                ->whereJsonContains('referensi_jadwal_users.staf', $user)
                ->where('referensi_jadwal_shift.deleted_at',null)
                ->get();

        $jabatan = ref_jabatan::leftJoin('referensi_jadwal_users', function($join) {
                    $join->on('referensi_jadwal_users.pegawai_id', '=', 'referensi_jadwal_users_jabatan.pegawai_id')
                        ->whereNull('referensi_jadwal_users.deleted_at');
                })
                ->select('referensi_jadwal_users_jabatan.*')
                ->whereJsonContains('referensi_jadwal_users.staf', $user)
                ->whereNull('referensi_jadwal_users_jabatan.deleted_at')
                ->get();

        $users = users::select('id', 'nama', 'name')
            ->whereNull('deleted_at')
            ->where('status', null)
            ->get();

        if ($jadwal) {
            $attributes = $jadwal->getAttributes();
            $rekanKerjaArray = [];

            for ($i = 1; $i <= 31; $i++) {
                $key = str_pad($i, 2, '0', STR_PAD_LEFT);
                $field = 'tgl' . $i;

                $shiftUser = $attributes[$field] ?? "";
                $jadwalArray[$key] = $shiftUser;

                if ($shiftUser != "") {
                    $rekans = jadwal_detail::where('id_jadwal', $jadwal->id_jadwal)
                        // ->where('pegawai_id', '!=', $jadwal->pegawai_id)
                        ->where($field, $shiftUser) // ✅ shift sama
                        ->whereNull('deleted_at')
                        ->pluck('pegawai_nama') // ✅ langsung ambil dari tabel
                        ->toArray();
                } else {
                    $rekans = [];
                }

                $rekanKerjaArray[$key] = $rekans;
            }
        }

        if (!empty($shift)) {
            // PUSH SHIFT & COLOR
            foreach ($shift as $key => $value) {
                $jamMasuk = Carbon::parse($value->berangkat)->format('H:i');
                $jamPulang = Carbon::parse($value->pulang)->format('H:i');

                $shiftArray[$value->singkat] = $value->shift ?? "";
                $refJam[$value->singkat] = "$jamMasuk - $jamPulang";
                // $shiftArray[$value->singkat] = $value->shift . ' (' . Carbon::parse($value->berangkat)->format('H:i') .'-'. Carbon::parse($value->pulang)->format('H:i') . ')' ?? "";

                // Warna HEX untuk shift dinamis
                if (Carbon::parse($value->pulang) > Carbon::parse($value->berangkat)) {
                    $colorArray[$value->singkat] = "#4CAF50"; // hijau
                } else { // LEWAT HARI
                    $colorArray[$value->singkat] = "#2196F3"; // biru
                }
            }

            // Tambahan shift khusus
            $shiftArray["L"] = "Libur";
            $shiftArray["C"] = "Cuti Tahunan";
            $shiftArray["CM"] = "Cuti Melahirkan";
            $shiftArray["CD"] = "Cuti Diluar Tanggungan";
            $shiftArray["CU"] = "Cuti Umroh";
            $shiftArray["CH"] = "Cuti Haji";

            $refJam["L"] = "";
            $refJam["C"] = "";
            $refJam["CM"] = "";
            $refJam["CD"] = "";
            $refJam["CU"] = "";
            $refJam["CH"] = "";

            // Warna HEX untuk shift khusus
            $colorArray["L"] = "#9E9E9E"; // grey
            $colorArray["C"] = "#FF9800"; // orange
            $colorArray["CM"] = "#E91E63"; // pink
            $colorArray["CD"] = "#F44336"; // red
            $colorArray["CU"] = "#009688"; // teal
            $colorArray["CH"] = "#3F51B5"; // indigo
        }

        // 1. Buat map user by ID
        $usersMap = [];
        foreach ($users as $user) {
            $usersMap[$user->id] = $user;
        }

        // 2. Buat map jabatan by id_staf
        $jabatanMap = [];
        foreach ($jabatan as $jab) {
            $jabatanMap[$jab->id_staf] = $jab;
        }

        // 3. Loop bawahan + build staf
        $staf = [];
        foreach (json_decode($jadwal->bawahan) as $id) {
            if (isset($usersMap[$id])) {
                $user = $usersMap[$id];
                if (isset($jabatanMap[$id])) {
                    $jab = $jabatanMap[$id];
                    $staf[] = $user->nama . ' (' . $jab->jabatan . ')';
                } else {
                    $staf[] = $user->nama ?? $user->name;
                }
            }
        }

        return response()->json([
            "jadwal" => $jadwalArray,
            "rekan_kerja" => $rekanKerjaArray,
            "ref_shift" => $shiftArray,
            "ref_jam" => $refJam,
            "color" => $colorArray,
            "flow" => [
                "Admin Jadwal" => $jadwal->nama_admin ?? "",
                "Tgl Dibuat" => $jadwal->tgl_dibuat ? $this->convertTgl($jadwal->tgl_dibuat) : "",
                "Verifikator" => $jadwal->nama_verif ?? "",
                "Tgl Verifikasi" => $jadwal->tgl_verif ? $this->convertTgl($jadwal->tgl_verif) : "",
                "Validator" => $jadwal->nama_valid ?? "",
                "Tgl Validasi" => $jadwal->tgl_valid ? $this->convertTgl($jadwal->tgl_valid) : "",
                "Daftar Staf" => $staf,
            ],
        ]);
    }

    public static function convertTgl($datetime)
    {
        // Buat instance Carbon
        $carbon = Carbon::parse($datetime);

        // Ubah ke timezone kalau mau
        $carbon->setTimezone('Asia/Jakarta');

        // Format
        $formatted = $carbon->translatedFormat('j F Y H.i') . ' WIB';

        return $formatted;
    }
}
