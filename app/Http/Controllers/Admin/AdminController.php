<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Setting;
use App\Models\MasterSweetner;
use App\Models\Diesel;
use Auth, Mail;

class AdminController extends Controller
{
    public function dashboard() {
        $petrolPurchased = MasterSweetner::where('fuel_type', 'Petrol')->sum('total_litres');
        $dieselPurchased  = MasterSweetner::where('fuel_type', 'Diesel')->sum('total_litres');

        $petrolConsumed = Diesel::where('type', 'Petrol')->where('source', 'Master Sweetner')->sum('litres');
        $dieselConsumed = Diesel::where('type', 'Diesel')->where('source', 'Master Sweetner')->sum('litres');

        $petrolBalance = $petrolPurchased - $petrolConsumed;
        $dieselBalance = $dieselPurchased - $dieselConsumed;

        return view('admin.dashboard', compact("petrolBalance", "dieselBalance"));
    }

    public function login(Request $request) {

        if ($request->method() == 'POST') {
            $validator = Validator::make($request->all(), [
                'email'    => 'required|email',
                'password' => 'required'
            ]);
            if ($validator->fails()){
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $user = User::where('email', $request->input('email'))->where('status', 1)->first();
           
            if ($user != null) {

                if (Hash::check($request->input('password'), $user->password)) {
                    Auth::login($user);
                    return redirect(route('admin.dashboard'));
                    // if($user->role_id == 1) {
                    // } else {
                    //     return redirect('/');
                    // }
                    // return redirect(route('admin.dashboard'));
                } else {
                    return back()->withErrors(['password' => 'invalid email or password']);
                }
           
            } else {
                return back()->withErrors(['password' => 'invalid email or password']);
            }
        }
        return view('login');
    }
}
