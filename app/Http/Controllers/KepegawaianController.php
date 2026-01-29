<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KepegawaianController extends Controller
{
    public function index()
    {
        // 1. Ambil daftar unit untuk sidebar
        $units = DB::table('unit')
                    ->select('unit_id', 'unit_nama')
                    ->orderBy('unit_nama', 'asc')
                    ->get();

        // 2. Hitung total semua pegawai dari database sdm_magang
        // Asumsi tabel bernama 'pegawai'
        $totalPegawai = DB::table('pegawai')->count();

        // Kirim data ke view
        return view('dashboard', compact('units', 'totalPegawai'));
    }
}