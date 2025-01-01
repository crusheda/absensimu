<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

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
    public function index()
    {
        $agent = new Agent();

        $data = [
            'agent' => $agent,
        ];

        return view('pages.index')->with('list',$data);
    }

    function manifest()
    {
        $ico = [
            "src"=> "assets/images/pwa.png",
            "sizes"=> "200x200",
            "type"=> "image/png"
        ];
        $data = [
            "background_color" => "#40189d",
            "description" => "Jobie - Job Portal Mobile App Template.",
            "display" => "fullscreen",
            "icons" => [$ico],
            "name" => "Jobie - Job Portal",
            "short_name" => "Jobie",
            "start_url" => "/mobile-app/xhtml/index.html"
        ];
        return response()->json($data);
    }
}
