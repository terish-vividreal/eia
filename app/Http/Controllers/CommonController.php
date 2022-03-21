<?php

namespace App\Http\Controllers;

use Form;
use Illuminate\Http\Request;
use App\Models\User;
use Response;
use Session;
use Carbon;
use DB;

use Auth;

class CommonController extends Controller
{

    /** Check user email id is unique. */
    public function isUniqueEmail(Request $request)
    { 
        if ($request->user_id == 0) {
            $count = User::where('email', $request->email)->count();
            echo ($count > 0 ? 'false' : 'true');
        } else {
            $count = User::where('email', $request->email)->where('id', '!=' , $request->user_id)->count();
            echo ($count > 0 ? 'false' : 'true');
        }
    }

    /** Check user mobile number is unique. */
    public function isUniqueMobile(Request $request)
    { 
        if ($request->user_id == 0) {
            $count = User::where('mobile', $request->mobile)->count();
            echo ($count > 0 ? 'false' : 'true');
        } else {
            $count = User::where('mobile', $request->mobile)->where('id', '!=' , $request->user_id)->count();
            echo ($count > 0 ? 'false' : 'true');
        }
    }

}
