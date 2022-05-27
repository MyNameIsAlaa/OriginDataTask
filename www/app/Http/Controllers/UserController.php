<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\User;


class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //



    public function login(Request $request)
    {
        $this->validate($request, [
        'email' => 'required',
        'password' => 'required'
         ]);

       $user = User::where('email', $request->input('email'))->first();

       if(Hash::check($request->input('password'), $user->password)){

           $apikey = base64_encode(Str::random(40));

           User::where('email', $request->input('email'))->update(['api_key' => "$apikey"]);

           return response()->json(['status' => 'success','api_key' => $apikey]);
           
       }else{
           return response()->json(['status' => 'fail'],401);
       }
    }

    

}
