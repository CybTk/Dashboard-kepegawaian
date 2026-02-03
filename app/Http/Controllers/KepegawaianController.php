<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KepegawaianController extends Controller
{
    public function index(Request $request)
    {
        try {
            $units = DB::table('unit')
                        ->select('unit_id', 'unit_nama')
                        ->orderBy('unit_nama', 'asc')
                        ->get();

            $unitId = $request->query('unit_id');

            // Jika unit_id adalah 'all' atau kosong, ambil total semua pegawai
            if ($unitId && $unitId !== 'all') {
                $totalPegawai = DB::table('pegawai_unit_trx')
                                    ->where('put_unit_id', $unitId)
                                    ->count();
            } else {
                $totalPegawai = DB::table('pegawai')->count();
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'total' => number_format($totalPegawai, 0, ',', '.')
                ]);
            }

            return view('dashboard', compact('units', 'totalPegawai'));

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kesalahan Database: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }
}