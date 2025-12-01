<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdiController extends Controller
{
    // Menampilkan list semua prodi
    public function index()
    {
        $prodi = DB::select("SELECT * FROM mst_prodi ORDER BY nama_prodi ASC");

        return response()->json([
            'success' => true,
            'message' => 'List prodi berhasil didapatkan',
            'data' => $prodi
        ]);
    }


    public function store(Request $request)
    {

        $mpid = $request->mpid;

        if (!empty($mpid)) {
            $existing = DB::selectOne("SELECT * FROM mst_prodi WHERE mpid = ? LIMIT 1", [$mpid]);
        
            if (!$existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data prodi tidak ditemukan'
                ], 404);
            }
            DB::update("
                UPDATE mst_prodi 
                SET kode_prodi = ?, nama_prodi = ?, fakultas = ?
                WHERE mpid = ?
            ", [
                $request->kode_prodi,
                $request->nama_prodi,
                $request->fakultas,
                $mpid
            ]);
            $updated = DB::selectOne("SELECT * FROM mst_prodi WHERE mpid = ?", [$mpid]);
            return response()->json([
                'success' => true,
                'message' => 'Prodi berhasil diperbarui',
                'data' => $updated
            ]);
        }

        // Validasi input
        $request->validate([
            'kode_prodi' => 'required|string|max:10',
            'nama_prodi' => 'required|string|max:100',
            'fakultas' => 'nullable|string|max:100',
        ]);

        // Cek apakah kode_prodi sudah ada
        $existing = DB::selectOne("SELECT * FROM mst_prodi WHERE kode_prodi = ? LIMIT 1", [$request->kode_prodi]);

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Kode prodi sudah ada'
            ], 409);
        }

        // Insert data baru
        $mpid = DB::table('mst_prodi')->insertGetId([
            'kode_prodi' => $request->kode_prodi,
            'nama_prodi' => $request->nama_prodi,
            'fakultas' => $request->fakultas
        ]);

        // Ambil data prodi yang baru saja ditambahkan
        $prodi = DB::selectOne("SELECT * FROM mst_prodi WHERE mpid = ?", [$mpid]);

        return response()->json([
            'success' => true,
            'message' => 'Prodi berhasil ditambahkan',
            'data' => $prodi
        ]);
    }


}

