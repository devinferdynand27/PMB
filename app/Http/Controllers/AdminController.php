<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class AdminController extends Controller
{
    public function dashboard()
    {
        $data = DB::select("
            SELECT 
                SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS diterima,
                SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) AS ditolak
            FROM mahasiswa
        ");
    
        return response()->json([
            'success' => true,
            'message' => 'Data dashboard mahasiswa',
            'results' => $data[0]
        ], 200);
    }
    

}
