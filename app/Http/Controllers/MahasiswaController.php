<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class MahasiswaController extends Controller
{
    public function store(Request $request)
    {
        try {

            $request->validate([
                'nama_lengkap' => 'required',
                'tanggal_lahir' => 'required|date',
                'alamat' => 'nullable',
                'no_telp' => 'nullable',
                'mpid' => 'nullable',
                'semester_masuk' => 'nullable|integer',
                'upload_file' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            ]);

            $fileName = null;
            if ($request->hasFile('upload_file')) {
                $file = $request->file('upload_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $destinationPath = public_path('mahasiswa');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $file->move($destinationPath, $fileName);
            }
            DB::insert("
                INSERT INTO mahasiswa 
                (nama_lengkap, tanggal_lahir, alamat, no_telp, mpid, semester_masuk, upload_file, nim, status, create_time, acc_time)
                VALUES (?, ?, ?, ?, ?, ?, ?, NULL, 0, NOW(), NULL)
            ", [
                $request->nama_lengkap,
                $request->tanggal_lahir,
                $request->alamat,
                $request->no_telp,
                $request->mpid,
                $request->semester_masuk,
                $fileName
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Mahasiswa berhasil ditambahkan'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function acc($mid){
    try {
        $nim = date('dmY') . $mid;  
        $accTime = date('Y-m-d H:i:s');
        $affected = DB::update("
            UPDATE mahasiswa
            SET nim = ?, acc_time = ?, status = 1
            WHERE mid = ?
        ", [
            $nim,
            $accTime,
            $mid
        ]);
        if ($affected === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil di-ACC',
            'nim' => $nim,
            'acc_time' => $accTime
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal ACC pendaftaran',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function getMahasiswaPending()
{
    $data = DB::select("select * from mahasiswa m join mst_prodi p on p.mpid = m.mid and m.status = 0;");
    $basePath = url('/mahasiswa');
    foreach ($data as $d) {
        if ($d->upload_file !== null) {
            $d->upload_file = $basePath . '/' . $d->upload_file;
        }
    }
    return response()->json([
        'success' => true,
        'message' => 'List mahasiswa status 0',
        'results' => $data
    ], 200);
}
    public function getMahasiswaAll()
    {
        $data = DB::select("select * from mahasiswa m join mst_prodi p on p.mpid = m.mid and m.status = 1;");
        $basePath = url('/mahasiswa');
        foreach ($data as $d) {
        if ($d->upload_file !== null) {
            $d->upload_file = $basePath . '/' . $d->upload_file;
        }
    }
    return response()->json([
        'success' => true,
        'message' => 'List mahasiswa status 0',
        'results' => $data
    ], 200);
    }

}
