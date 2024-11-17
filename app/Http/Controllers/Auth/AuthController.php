<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class AuthController extends Controller
{
    function index()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        } else {
            return view('pages.auth.signin');
        }
    }
}
