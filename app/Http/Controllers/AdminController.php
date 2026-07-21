<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $countries = Country::all();
        $users = User::all();
        return view('admin.manage', compact('countries', 'users'));
    }

    public function destroyCountry($id)
    {
        Country::destroy($id);
        return back()->with('success', 'Data negara berhasil dihapus!');
    }

    public function changeRole($id)
    {
        $user = User::findOrFail($id);
        $user->role = $user->role === 'admin' ? 'user' : 'admin';
        $user->save();

        return back()->with('success', 'Role user berhasil diperbarui!');
    }
}