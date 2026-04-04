<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        return view('settings.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:employees,username,' . $user->id,
        ], [
            'name.required' => __('messages.required'),
            'username.required' => __('messages.required'),
            'username.unique' => __('messages.username_taken'),
        ]);

        $user->update($validated);
        return back()->with('success', __('messages.profile_updated'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => __('messages.required'),
            'current_password.current_password' => __('messages.wrong_password'),
            'password.required' => __('messages.required'),
            'password.min' => __('messages.min_password'),
            'password.confirmed' => __('messages.password_mismatch'),
        ]);

        Auth::user()->update(['password' => Hash::make($request->input('password'))]);
        return back()->with('success', __('messages.password_updated'));
    }
}
