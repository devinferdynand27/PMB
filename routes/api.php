<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MahasiswaController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/_act_login', [AuthController::class, 'login']);
Route::post('/_act_register', [AuthController::class, 'register']);


//dashboard admin
Route::get('/mahasiswa-dashboard', [AdminController::class, 'dashboard']);

//prodi
Route::get('/prodi', [ProdiController::class, 'index']);
Route::post('/_act_add_prodi', [ProdiController::class, 'store']);
Route::put('/prodi_act_edit/{id}', [ProdiController::class, 'update']);

//mahasiswa
Route::post('/mahasiswa-pendaftaran', [MahasiswaController::class, 'store']);
Route::put('/mahasiswa/acc/{mid}', [MahasiswaController::class, 'acc']);
Route::put('/mahasiswa/non-acc/{mid}', [MahasiswaController::class, 'no_acc']);
Route::get('/mahasiswa/pending', [MahasiswaController::class, 'getMahasiswaPending']);
Route::get('/mahasiswa-all', [MahasiswaController::class, 'getMahasiswaAll']);


Route::get('/identitas-mahasiswa/{pid}', [MahasiswaController::class, 'GetDataMahasiswaPID']);
