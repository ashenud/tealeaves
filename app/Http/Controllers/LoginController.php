<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    
    public function login(Request $request) {
        
        $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {

            if(Auth::user()->status=='1') {

                $token = Str::random(60);

                $request->user()->forceFill([
                    'api_token' => Hash::make($token),
                ])->save();

                if(Auth::user()->role_id==md5('1')) {
                    return response()->json([
                        'result' => true,
                        'message' => 'You have successfully logged in !',
                        'data'=> [ 'token' => $token,'role_id' => Auth::user()->role_id],
                    ]);
                }
                else {
                    return response()->json([
                        'result' => false,
                        'message' => 'You have no permission to access here !',
                    ]);
                }
            }
            else {
                return response()->json([
                    'result' => false,
                    'message' => 'Your account has been deactivated !',
                ]);
            }

        }
        else {
            return response()->json([
                'result' => false,
                'message' => 'Username or password you have entered is incorrect !',
            ]);
        }

    }

    public function logout(Request $request) {
        
        Auth::logout();
        Session::flush();
        return redirect('/');

    }

}
