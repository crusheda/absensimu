<?php

namespace App\Http\Controllers\Android;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\users;
use Jenssegers\Agent\Agent;
use Carbon\Carbon;
use Auth,Validator,Redirect,Response,File,Storage;

class SettingController extends Controller
{
    function profilUser($id_user)
    {
        $user = users::where('id',$id_user)->first(); // atau pakai model kalau ada

        return response()->json([
            'user' => $user,
        ]);
    }
}
