<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use App\Models\Country;
use App\Models\User;
use Validator;
use Auth;
use Hash;
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
        return view('home', compact('page', 'variants', 'user'));
    }
}
