<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    function manifest()
    {
        $ico = [
            "src"=> "images/logo.png",
            "sizes"=> "200x200",
            "type"=> "image/png"
        ];
        $data = [
            "background_color" => "#40189d",
            "description" => "Aplikasi Absensi Terintegrasi Rumah Sakit PKU Muhammadiyah Sukoharjo",
            "display" => "fullscreen",
            "icons" => [$ico],
            "name" => "E-Absensi | Simrsmu v.3",
            "short_name" => "E-Absensi",
            "start_url" => "/"
        ];
        return response()->json($data);
    }
}
