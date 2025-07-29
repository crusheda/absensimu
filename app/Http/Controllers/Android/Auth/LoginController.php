<?php

namespace App\Http\Controllers\Android\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\users_foto;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::leftJoin('users_foto','users_foto.user_id','=','users.id')
                    ->select('users.*','users_foto.title as title_foto_profil','users_foto.filename as foto_profil')
                    ->where('users.name', $request->username)
                    ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Login gagal: Akun Anda tidak ditemukan pada Database Kami',
            ], 401);
        }

        if (!$user->nip) {
            return response()->json([
                'message' => 'Login gagal: NIP Anda belum terinput di Database Kepegawaian',
            ], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Login gagal: Username atau Password salah',
            ], 401);
        }

        if ($user->deleted_at !== null || $user->status == 1) {
            return response()->json([
                'message' => 'Login gagal: Akun Anda sudah Nonaktif',
            ], 401);
        }

        // Buat token
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token, // ← Kirim token ke Flutter
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

        // code lama
        // $request->user()->tokens()->delete(); // jika pakai sanctum
        // return response()->json(['message' => 'Berhasil logout']);
    }
}
