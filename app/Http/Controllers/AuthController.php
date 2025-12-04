<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    // funsgi untuk login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $user = DB::selectOne(
            "SELECT * FROM person WHERE email = ? LIMIT 1",
            [$request->email]
        );
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak ditemukan'
            ], 401);
        }

        // Cek password (hashed)
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password salah'
            ], 401);
        }
        unset($user->password);
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => $user
        ]);
    }

    //Fungsi untuk registrasi 
    public function register(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'password' => 'required|string|min:6',
        ]);
        $existingUser = DB::selectOne(
            "SELECT * FROM person WHERE email = ? LIMIT 1",
            [$request->email]
        );

        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'Email sudah terdaftar'
            ], 409); 
        }
        $hashedPassword = Hash::make($request->password);
        $pid = DB::table('person')->insertGetId([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => $hashedPassword
        ]);
        
        $user = DB::selectOne("SELECT * FROM person WHERE pid = ?", [$pid]);
        if (isset($user->password)) {
            unset($user->password);
        }
        return response()->json([
            'success' => true,
            'message' => 'Register berhasil',
            'data' => $user
        ]);
    }
}
