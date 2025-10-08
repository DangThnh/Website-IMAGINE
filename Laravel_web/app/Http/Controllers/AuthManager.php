<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class AuthManager extends Controller
{
    function login()
    {
        /*if(Auth::check()){
            return redirect(route('gallery'));
        }*/
        return view('login');
    }

    function registration()
    {
        /*if(Auth::check()){
            return redirect(route('gallery'));
        }*/
        return view('registration');
    }
    function loginPost(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended(route('gallery'));
        }
        return redirect(route('login'))->with("error", "Login details are not valid");
    }

    function registrationPost(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['password'] = Hash::make($request->password);
        $data['avatar'] = url("assets/pictures/default-avatar.png");

        $user = User::create($data);

        if (!$user) {
            return redirect(route('registration'))->with("error", "Registration failed, try again.");
        }

        return redirect(route('login'))->with("success", "Registration success, login to access your account");
    }


    function logout()
    {
        Session::flush();
        Auth::logout();
        return redirect(route('login'));
    }
}
