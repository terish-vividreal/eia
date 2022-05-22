<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Document;
use App\Models\Permit;
use App\Models\User;
use App\Models\Eia;
use DB;

class HomeController extends Controller
{
    protected $title    = 'Dashboard';
    protected $viewPath = '';
    protected $route    = '';

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
        $page                   = collect();
        $variants               = collect();
        $user                   = auth()->user();
        $page->title            = "Dashboard";
        $page->route            = "profile";
        $variants->projects     = Project::get();
        $variants->eia          = Eia::get();
        $variants->documents    = Document::get();
        $variants->permits      = Permit::whereActive(1)->get();
        return view('home', compact('page', 'variants', 'user'));
    }
}
