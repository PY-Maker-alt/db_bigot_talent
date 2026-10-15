<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lomba;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // --- BAGIAN TAMPILAN HALAMAN (DASHBOARD & MENU TERPISAH) ---

    // 1. Halaman Dashboard Utama Admin
    public function index()
    {
        $lombas = Lomba::all();
        $pendaftarans = Pendaftaran::with(['user', 'lomba'])->get();
        
        // Ambil semua data user yang berstatus 'siswa'
        $siswas = User::where('role', 'siswa')->get();
        
        return view('admin.dashboard', compact('lombas', 'pendaftarans', 'siswas'));
    }

    // 2. Halaman Khusus Kelola Lomba
    public function kelolaLomba() 
    {
        $lombas = Lomba::all();
        return view('admin.kelola_lomba', compact('lombas'));
    }

    // 3. Halaman Khusus Kelola Siswa
    public function kelolaSiswa() 
    {
        $siswas = User::where('role', 'siswa')->get();
        return view('admin.kelola_siswa', compact('siswas'));
    }

    // 4. Halaman Khusus Kelola Status Pendaftaran
    public function kelolaStatus() 
    {
        $pendaftarans = Pendaftaran::with(['user', 'lomba'])->get();
        return view('admin.kelola_status', compact('pendaftarans'));
    }


    // --- BAGIAN PROSES DATA (CRUD LOMBA & STATUS) ---

    // Menyimpan Data Mata Lomba Baru (Create)
    public function storeLomba(Request $request) 
    {
        Lomba::create($request->all());
        return back()->with('success', 'Mata Lomba berhasil ditambahkan!');
    }

    // Mengubah Status Pendaftaran Siswa (Update)
    public function updateStatus(Request $request, $id) 
    {
        $pendaftaran = Pendaftaran::findOrFail($id);
        $pendaftaran->update(['status' => $request->status]);
        return back()->with('success', 'Status pendaftaran berhasil diubah!');
    }


    // --- BAGIAN PROSES DATA (CRUD AKUN SISWA) ---

    // CREATE (Tambah Siswa)
    public function storeSiswa(Request $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Enkripsi password
            'role' => 'siswa'
        ]);
        return back()->with('success', 'Akun siswa berhasil dibuat!');
    }

    // Tampilkan Halaman EDIT Siswa
    public function editSiswa($id)
    {
        $siswa = User::findOrFail($id);
        return view('admin.edit_siswa', compact('siswa'));
    }

    // UPDATE (Simpan Perubahan Siswa)
    public function updateSiswa(Request $request, $id)
    {
        $siswa = User::findOrFail($id);
        $siswa->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update password hanya jika kolom password diisi
        if($request->filled('password')) {
            $siswa->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Data siswa berhasil diperbarui!');
    }

    // DELETE (Hapus Siswa)
    public function destroySiswa($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'Akun siswa berhasil dihapus!');
    }
}