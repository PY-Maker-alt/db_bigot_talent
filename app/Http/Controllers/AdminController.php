<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lomba;
use App\Models\Pendaftaran;

class AdminController extends Controller
{
    // 1. Menampilkan Halaman Dashboard Admin
    public function index()
    {
        // Ambil semua data lomba dari database
        $lombas = Lomba::all();
        
        // Ambil semua data pendaftaran, SERTA bawa juga data nama siswa(user) dan nama lomba-nya
        $pendaftarans = Pendaftaran::with(['user', 'lomba'])->get();
        
        // Kirim datanya ke halaman (view) admin.dashboard
        return view('admin.dashboard', compact('lombas', 'pendaftarans'));
    }

    // 2. Menyimpan Data Mata Lomba Baru (Create)
    public function storeLomba(Request $request)
    {
        // Simpan semua inputan dari form web ke tabel lomba
        Lomba::create($request->all());
        
        // Kembalikan admin ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Mata Lomba berhasil ditambahkan!');
    }

    // 3. Mengubah Status Pendaftaran Siswa (Update)
    public function updateStatus(Request $request, $id)
    {
        // Cari data pendaftaran berdasarkan ID yang diklik
        $pendaftaran = Pendaftaran::findOrFail($id);
        
        // Ubah isi kolom 'status' dengan status terbaru dari form dropdown
        $pendaftaran->update(['status' => $request->status]);
        
        // Kembalikan ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Status pendaftaran berhasil diubah!');
    }
}