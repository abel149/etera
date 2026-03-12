<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Models\BankAccount;


class ProfileController extends Controller
{
    // Show the profile edit form (optional)
    public function edit()
    {
        // Retrieve the logged-in user
        $user = Auth::user();

        // Return the view with the user's data
        return view('profile.edit', compact('user'));
    }




public function updateSelf(Request $request)
{
    $user = auth()->user();

    // Normalize empty strings to null (common when email is optional)
    $request->merge([
        'email' => $request->filled('email') ? $request->email : null,
        'phone_number' => $request->filled('phone_number') ? $request->phone_number : null,
        'tin_number' => $request->filled('tin_number') ? $request->tin_number : null,
    ]);

    $request->validate([
        'name' => 'required|string|max:255',
        'phone_number' => 'nullable|string|max:20',
        'email' => 'nullable|email|unique:users,email,' . $user->id,
        'tin_number' => 'nullable|string|max:100',
        // 'business_license_number' => 'nullable|string|max:100',
        // 'license_expire_date' => 'nullable|date',
        'stamp_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'license_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $user->update($request->only([
        'name',
        'phone_number',
        'email',
        'tin_number',
        // 'business_license_number',
        // 'license_expire_date',
    ]));

    if ($request->hasFile('stamp_image')) {
        if ($user->stamp_image && Storage::disk('public')->exists($user->stamp_image)) {
            Storage::disk('public')->delete($user->stamp_image);
        }

        $path = $request->file('stamp_image')->store('stamps', 'public');
        $user->stamp_image = $path;
    }

    if ($request->hasFile('license_image')) {
        if ($user->license_image && Storage::disk('public')->exists($user->license_image)) {
            Storage::disk('public')->delete($user->license_image);
        }

        $path = $request->file('license_image')->store('licenses', 'public');
        $user->license_image = $path;
    }

    // Handle password update
    if ($request->filled('current_password')) {
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->password = Hash::make($request->password);
    }

    $user->save();

    return back()->with('success', 'Profile updated successfully.');
}



    // Existing methods...

    // Add bank account
    public function storeBank(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
        ]);

        auth()->user()->bankAccounts()->create($request->all());

        return back()->with('success', 'Bank account added successfully.');
    }

    // Update bank account
    public function updateBank(Request $request, BankAccount $bank)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
        ]);

        $bank->update($request->all());

        return back()->with('success', 'Bank account updated successfully.');
    }



}
