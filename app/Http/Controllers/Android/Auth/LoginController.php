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

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Login gagal: username atau password salah',
            ], 401);
        }

        // Buat token
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token, // ← Kirim token ke Flutter
            'user' => [
                'id' => $user->id,
                'nip' => $user->nip,
                'name' => $user->name,
                'nama' => $user->nama,
                'foto_profil' => $user->foto_profil ?? null,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete(); // jika pakai sanctum
        return response()->json(['message' => 'Berhasil logout']);
    }
}
