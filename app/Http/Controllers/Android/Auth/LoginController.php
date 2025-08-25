<?php

namespace App\Http\Controllers\Android\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\users_foto;
use Illuminate\Support\Facades\Hash;
use Google\Client;
use Google\Service\PlayIntegrity;
use DB,Auth,Validator,Redirect,Response,File,Storage;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        if (!$request->username) {
            return response()->json([
                'message' => 'Anda belum memasukkan Kredensial Login. Periksa Isian Username Anda.',
            ], 401);
        }
        if (!$request->password) {
            return response()->json([
                'message' => 'Anda belum memasukkan Kredensial Login. Periksa Isian Password Anda.',
            ], 401);
        }

        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'device_id'  => 'required',
            'token' => 'required|string',
            'platform' => 'required|string',      // 'android' / 'ios'
            'os_version' => 'nullable|string',    // versi OS
            'model' => 'nullable|string',       // model device
            'is_rooted' => 'nullable|string',       // model device
        ]);

        $user = User::leftJoin('users_foto','users_foto.user_id','=','users.id')
                    ->select('users.*','users_foto.title as title_foto_profil','users_foto.filename as foto_profil')
                    ->where('users.name', $request->username)
                    ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Akun Anda tidak ditemukan pada Database Kami',
            ], 401);
        }

        if (!$user->nip) {
            return response()->json([
                'message' => 'NIP Anda belum terinput di Database Kepegawaian',
            ], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Username atau Password salah',
            ], 401);
        }

        if ($user->deleted_at !== null || $user->status == 1) {
            return response()->json([
                'message' => 'Akun Anda sudah Nonaktif',
            ], 401);
        }

        // 🔒 cek device aktif ada di DB
        $existingDevice = DB::table('fcm_tokens')
            ->where('user_id', $user->id)
            // ->where('device_id', '!=', null)
            // ->where('is_active', 1)
            // ->where('accepted', 1)
            ->whereNull('deleted_at')
            ->orderBy('id','DESC')
            ->first();

        if ($existingDevice) {
            if (!$existingDevice->status) {
                return response()->json([
                    'message' => 'Perangkat Anda telah DIBLOKIR dari Sistem Absensi RS PKU Muhammadiyah Sukoharjo. Silakan konfirmasi kepada bagian terkait.',
                ], 403);
            }

            // jika device_id beda -> tolak login
            if ($existingDevice->device_id !== $request->device_id) {
                if (!$existingDevice->accepted) {
                    DB::table('fcm_tokens')->where('id', $existingDevice->id)->update([
                        'device_id'    => $request->device_id,
                        // 'is_active'    => 0,
                    ]);
                    return response()->json([
                        'message' => 'Perangkat baru Anda belum disetujui untuk melakukan Absensi. Silakan konfirmasi ulang kepada bagian terkait.',
                    ], 403);
                } else {
                    return response()->json([
                        'message' => 'Maaf, Akun Anda terdeteksi sudah didaftarkan di perangkat lain. Silakan konfirmasi ulang kepada bagian terkait.',
                    ], 403);
                }
            }

            // cek apakah device disetujui (accepted = 1)
            if (!$existingDevice->accepted) {
                return response()->json([
                    'message' => 'Perangkat ini belum disetujui untuk melakukan Absensi. Silakan konfirmasi terlebih dahulu kepada bagian terkait.',
                ], 403);
            }

            // update token dan informasi device
            DB::table('fcm_tokens')->where('id', $existingDevice->id)->update([
                'token'        => $request->token,
                'platform'     => $request->platform,
                'os_version'   => $request->os_version,
                'model'        => $request->model,
                'last_login_at'=> now(),
                'updated_at'   => now(),
                'is_active'    => 1,
            ]);
        } else {
            // device baru → insert dulu dengan accepted = 0 (butuh approval admin)
            DB::table('fcm_tokens')->insert([
                'user_id'      => $user->id,
                'device_id'    => $request->device_id,
                'token'        => $request->token,
                'platform'     => $request->platform,
                'os_version'   => $request->os_version,
                'model'        => $request->model,
                'is_active'    => 0,
                'is_rooted'    => $request->is_rooted,
                'ip_address'   => $request->ip_address ?? $request->ip(),
                'accepted'     => 0, // default 0 → harus di-approve
                'last_login_at'=> now(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            return response()->json([
                'message' => 'Perangkat baru terdeteksi, menunggu persetujuan dari bagian SDI.',
            ], 403);

            // kalau belum ada device aktif → cek apakah device ini sudah pernah terdaftar
            // $deviceRow = DB::table('fcm_tokens')
            //     ->where('user_id', $user->id)
            //     ->where('device_id', $request->device_id)
            //     ->where('status',true)
            //     ->whereNull('deleted_at')
            //     ->orderBy('id','DESC')
            //     ->first();

            // if ($deviceRow) {
            //     // cek apakah disetujui
            //     if (!$deviceRow->accepted) {
            //         return response()->json([
            //             'message' => 'Login gagal: Perangkat ini belum disetujui oleh bagian SDI',
            //         ], 403);
            //     }

            //     // update ulang device lama
            //     DB::table('fcm_tokens')->where('id', $deviceRow->id)->update([
            //         'token'        => $request->token,
            //         'platform'     => $request->platform,
            //         'os_version'   => $request->os_version,
            //         'model'        => $request->model,
            //         'last_login_at'=> now(),
            //         'updated_at'   => now(),
            //         'is_active'    => 1,
            //     ]);
            // } else {
            //     // device baru → insert dulu dengan accepted = 0 (butuh approval admin)
            //     DB::table('fcm_tokens')->insert([
            //         'user_id'      => $user->id,
            //         'device_id'    => $request->device_id,
            //         'token'        => $request->token,
            //         'platform'     => $request->platform,
            //         'os_version'   => $request->os_version,
            //         'model'        => $request->model,
            //         'is_active'    => 0,
            //         'is_rooted'    => $request->is_rooted,
            //         'ip_address'   => $request->ip_address ?? $request->ip(),
            //         'accepted'     => 0, // default 0 → harus di-approve
            //         'last_login_at'=> now(),
            //         'created_at'   => now(),
            //         'updated_at'   => now(),
            //     ]);

            //     return response()->json([
            //         'message' => 'Login gagal: Perangkat baru terdeteksi, menunggu persetujuan bagian SDI',
            //     ], 403);
            // }
        }

        // Buat token
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'id_user' => $user->id,
                'nip' => $user->nip,
                'name' => $user->name,
                'nama' => $user->nama ?? $user->name,
                'foto_profil' => $user->foto_profil ?? '',
            ],
        ]);
    }

    public function logout(Request $request)
    {
        try {
            if ($request->user()) {
                // tandai device nonaktif
                DB::table('fcm_tokens')
                    ->where('user_id', $request->user()->id)
                    ->where('device_id', $request->device_id) // device yang sedang dipakai
                    ->update(['is_active' => 0]);

                // Hapus semua token milik user ini
                $request->user()->tokens()->delete();
                return response()->json(['message' => 'Berhasil logout'], 200);
            } else {
                // Kalau user() null (token invalid/expired)
                return response()->json(['message' => 'Token tidak valid atau sudah logout'], 401);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan logout',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // public function verify_integrity()
    // {
    //     $request->validate([
    //         'token' => 'required|string',
    //     ]);

    //     $token = $request->input('token');

    //     try {
    //         $client = new Client();
    //         $client->setAuthConfig(storage_path('app/fcmabsensi-11e43a71d377.json'));
    //         $client->addScope(PlayIntegrity::PLAYINTEGRITY);

    //         $integrityService = new PlayIntegrity($client);
    //         $decoded = $integrityService->v1->decodeIntegrityToken([
    //             'tokenPayload' => $token
    //         ]);

    //         // Cek app integrity / device integrity
    //         $isValid = $decoded->appIntegrity->appRecognitionVerdict === 'PLAY_RECOGNIZED';

    //         return response()->json(['valid' => $isValid]);
    //     } catch (\Exception $e) {
    //         return response()->json(['valid' => false, 'error' => $e->getMessage()], 400);
    //     }
    // }
}
