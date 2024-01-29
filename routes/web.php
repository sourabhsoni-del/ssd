<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', function () {
    return view('login');
});

Route::post('login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password])) {
        $user = Auth::user();
        $request->session()->regenerate();

        if ($user->role) {
            $roleName = $user->role->name;

            if ($roleName === 'SuperAdmin') {
                return "Login Successful - Super Admin";
            } elseif ($roleName === 'User') {
                return 'Login Successful - User';
            } elseif ($roleName === 'Admin') {
                return 'Login Successful - Admin';
            } else {
                return redirect()->back()->with('fail', 'Login failed: User does not have a valid role.');
            }
        } else {
            // Add your logic for users without a role
            return redirect()->back()->with('fail', 'Login failed: User does not have a valid role.');
        }
    } else {
        // Authentication failed
        return redirect()->back()->with('fail', 'Login failed: Invalid credentials.');
    }
});

Route::get('logout', function () {
    Auth::guard('web')->logout();
    return redirect('/');
});
