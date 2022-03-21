<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use Illuminate\Support\Str;
use App\Rules\MatchOldPassword;
use App\Models\User;
use Validator;

class ProfileController extends Controller
{
    protected $title    = 'Profile';
    protected $viewPath = 'profile';
    protected $route    = 'profile';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page                   = collect();
        $variants               = collect();
        $user                   = auth()->user();
        $page->title            = $this->title;
        $page->link             = url($this->route);
        $page->route            = $this->route;  
        $variants->phoneCode    = Country::select("id", \DB::raw('CONCAT(" +", phonecode, " (", name, ")") AS phone_code'))->pluck('phone_code', 'id');
        return view($this->viewPath . '.profile', compact('page', 'variants', 'user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), 
                            [ 'name' => 'required', 'email' => 'required|email|unique:users,email,'.$id, 'mobile' => 'nullable|min:3|max:15|regex:/^([0-9\s\-\+\(\)]*)$/|unique:users,email,'.$id],
                            [ 'name.required' => 'Please enter First name', 'email.required' => 'Please enter E-mail'] );

        if ($validator->passes()) {
            $input                  = $request->all();
            $user                   = User::find($id);  
            $input['mobile']        = Str::remove(' ', $input['mobile']);     
            $user->update($input);
            return ['flagError' => false, 'message' => $this->title. "  updated successfully"];
        }
        return ['flagError' => true, 'message' => "Errors occurred Please check !",  'error'=> $validator->errors()->all()];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_password_confirmation' => ['same:new_password'],
        ]);
        if ($validator->passes()) {

            if(strcmp($request->get('old_password'), $request->get('new_password')) == 0){
                $errors = array('Old and new passwords cannot be same !');
                return ['flagError' => true, 'message' => "Errors Occurred. Please check!",  'error'=>$errors];
            }

            User::find(auth()->user()->id)->update(['password'=> \Hash::make($request->new_password)]);
            return ['flagError' => false, 'message' => "Password updated successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occurred. Please check!",  'error'=> $validator->errors()->all()];
    }
}
