<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\Test;
use App\Models\User;

class testController extends Controller
{
    public function sendNotification()
    {
        $user = User::find(1); // Temukan pengguna berdasarkan ID atau kondisi lain

        if ($user) {
            // Kirimkan notifikasi
            $user->notify(new Test());
        } else {
            // Tangani jika user tidak ditemukan
            return response()->json(['message' => 'User not found'], 404);
        };
    }
}
