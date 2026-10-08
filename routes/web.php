<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\ProfileController;

// 1. Rute Halaman Depan (Welcome Page)
Route::get('/', function () {
    return view('welcome');
});

// 2. Rute "Penyortir" Pintar (Otomatis arahkan sesuai Role sesaat setelah Login)
Route::get('/dashboard', function () {
    if (Auth::user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('siswa.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. JALUR KHUSUS ADMIN (Dijaga oleh Middleware Role:admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Halaman Dashboard Admin
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Proses Simpan Lomba Baru
    Route::post('/admin/lomba', [AdminController::class, 'storeLomba'])->name('admin.lomba.store');
    
    // Proses Update Status Pendaftaran
    Route::post('/admin/pendaftaran/{id}/status', [AdminController::class, 'updateStatus'])->name('admin.status.update');
});

// 4. JALUR KHUSUS SISWA (Dijaga oleh Middleware Role:siswa)
Route::middleware(['auth', 'role:siswa'])->group(function () {
    // Halaman Dashboard Siswa
    Route::get('/siswa/dashboard', [SiswaController::class, 'index'])->name('siswa.dashboard');
    
    // Proses Daftar Lomba
    Route::post('/siswa/daftar', [SiswaController::class, 'daftarLomba'])->name('siswa.daftar');
});

// 5. Rute Edit Profil (Bawaan Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Bawaan Breeze untuk rute Login, Register, Logout, dll (JANGAN DIHAPUS)
require __DIR__.'/auth.php';