<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Login; // pastikan ada
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $nama = \App\Models\Admin::where('login_id', $user->id)->value('nama');
        } else {
            $nama = \App\Models\Siswa::where('login_id', $user->id)->value('nama');
        }

        return view('admin.profile', compact('user', 'nama'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        // Ambil hash password langsung dari DB supaya pasti fresh
        $currentHash = Login::where('id', $user->id)->value('password');
        if (! Hash::check($request->current_password, $currentHash)) {
            return back()->withErrors(['current_password' => 'Password lama salah']);
        }

        // Opsi A: Eloquent builder (tanpa ->save())
        Login::where('id', $user->id)->update([
            'password'   => Hash::make($request->new_password),
            'updated_at' => now(), // update() via builder tidak auto set timestamps
        ]);

        /* 
        // Opsi B: Query builder murni (juga tanpa ->save())
        DB::table('login')->where('id', $user->id)->update([
            'password'   => Hash::make($request->new_password),
            'updated_at' => now(),
        ]);
        */

        return back()->with('success', 'Password berhasil diubah');
    }
}
