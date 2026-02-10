<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KepegawaianController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Mengambil daftar unit untuk dropdown sidebar
            $units = DB::table('unit')
                        ->select('unit_id', 'unit_nama')
                        ->orderBy('unit_nama', 'asc')
                        ->get();

            $unitId = $request->query('unit_id');

            /**
             * 1. Query Dasar (Base Query)
             * Menjamin sinkronisasi data untuk Total, Kategori, JK, Pendidikan, Agama, Usia, Jabatan, dan Golongan.
             */
            $baseQuery = DB::table('pegawai as p')
                ->join('pegawai_unit_trx as put', 'p.pg_id', '=', 'put.put_pg_id')
                ->where(function($q) {
                    $q->where('p.pg_isdel', 'Tidak')
                      ->orWhereNull('p.pg_isdel');
                })
                ->whereNotNull('p.pg_kategori')
                ->where(function($q) {
                    $q->where('put.put_status', 'Aktif')
                      ->orWhereNull('put.put_status');
                });

            // 2. Filter Berdasarkan Unit
            if ($unitId && $unitId !== 'all') {
                $baseQuery->where('put.put_unit_id', $unitId);
            }

            // Hitung Total Pegawai
            $totalPegawai = (clone $baseQuery)->count();

            /**
             * 3. Ambil Data Distribusi Kategori (Dosen & Tendik)
             */
            $distribusi = (clone $baseQuery)
                ->select('p.pg_kategori', DB::raw('COUNT(p.pg_id) as jumlah'))
                ->groupBy('p.pg_kategori')
                ->get()
                ->pluck('jumlah', 'pg_kategori');

            $jmlDosen = $distribusi['Dosen'] ?? 0;
            $jmlTendik = $distribusi['Tendik'] ?? 0;

            /**
             * 4. Ambil Data Distribusi Jenis Kelamin
             */
            $dataJK = (clone $baseQuery)
                ->select('p.pg_kategori', 'p.pg_jk', DB::raw('COUNT(p.pg_id) as jumlah'))
                ->groupBy('p.pg_kategori', 'p.pg_jk')
                ->get();

            $genderStats = [
                'Tendik' => ['L' => 0, 'P' => 0],
                'Dosen'  => ['L' => 0, 'P' => 0]
            ];

            foreach ($dataJK as $row) {
                $jk = (in_array($row->pg_jk, ['Laki-laki', 'L'])) ? 'L' : 'P';
                if (isset($genderStats[$row->pg_kategori])) {
                    $genderStats[$row->pg_kategori][$jk] = $row->jumlah;
                }
            }

            /**
             * 5. Ambil Data Tingkat Pendidikan
             */
            $getPendidikan = function($kategori) use ($baseQuery) {
                return (clone $baseQuery)
                    ->join('pendidikan as pd', 'p.pg_id', '=', 'pd.pd_pg_id')
                    ->join('pendidikan_tingkat as pt', 'pd.pd_pdt_id', '=', 'pt.pdt_id')
                    ->where('p.pg_kategori', $kategori)
                    ->where(function($q) { $q->where('pd.pd_status_sk', 'Aktif')->orWhereNull('pd.pd_status_sk'); })
                    ->select('pt.pdt_nama', DB::raw('COUNT(p.pg_id) as jumlah'))
                    ->groupBy('pt.pdt_id', 'pt.pdt_nama')
                    ->orderBy('pt.pdt_id', 'asc')->get();
            };

            $pendidikanDosen = $getPendidikan('Dosen');
            $pendidikanTendik = $getPendidikan('Tendik');

            /**
             * 6. Ambil Data Distribusi Agama
             */
            $dataAgama = (clone $baseQuery)
                ->join('agama as ag', 'p.pg_agama_id', '=', 'ag.ag_id')
                ->select('p.pg_kategori', 'ag.ag_nama as agama', DB::raw('COUNT(p.pg_id) as jumlah'))
                ->groupBy('p.pg_kategori', 'ag.ag_nama')
                ->orderBy('ag.ag_id', 'asc')
                ->get();

            $getAgamaFormat = function($kategori) use ($dataAgama) {
                $filtered = $dataAgama->where('pg_kategori', $kategori);
                return [
                    'labels' => $filtered->pluck('agama')->toArray(),
                    'values' => $filtered->pluck('jumlah')->toArray()
                ];
            };

            $agamaDosen = $getAgamaFormat('Dosen');
            $agamaTendik = $getAgamaFormat('Tendik');

            /**
             * 7. Analisis Usia
             */
            $usiaStats = (clone $baseQuery)
                ->select(
                    'p.pg_kategori',
                    DB::raw("SUM(CASE WHEN TIMESTAMPDIFF(YEAR, p.pg_tgllahir, CURDATE()) BETWEEN 15 AND 64 THEN 1 ELSE 0 END) as produktif"),
                    DB::raw("SUM(CASE WHEN TIMESTAMPDIFF(YEAR, p.pg_tgllahir, CURDATE()) > 64 THEN 1 ELSE 0 END) as non_produktif"),
                    DB::raw("MAX(TIMESTAMPDIFF(YEAR, p.pg_tgllahir, CURDATE())) as maks_usia"),
                    DB::raw("MIN(TIMESTAMPDIFF(YEAR, p.pg_tgllahir, CURDATE())) as min_usia"),
                    DB::raw("ROUND(AVG(TIMESTAMPDIFF(YEAR, p.pg_tgllahir, CURDATE())), 0) as rata_rata_usia"),
                    DB::raw("SUM(CASE WHEN p.pg_tgllahir IS NULL THEN 1 ELSE 0 END) as tidak_diketahui")
                )
                ->groupBy('p.pg_kategori')
                ->get()
                ->keyBy('pg_kategori');

            /**
             * 8. Distribusi Jabatan Fungsional
             */
            $jabatanStats = (clone $baseQuery)
                ->leftJoin('jabatan_trx as jt', function($join) {
                    $join->on('p.pg_id', '=', 'jt.jt_pg_id')
                         ->where(function($q) {
                             $q->where('jt.jt_status_aktif', 'Aktif')->orWhereNull('jt.jt_status_aktif');
                         });
                })
                ->leftJoin('jabatan_ref as jr', 'jt.jt_jr_id', '=', 'jr.jr_id')
                ->select(
                    DB::raw("CASE WHEN p.pg_kategori = 'Tendik' THEN 'Tendik' ELSE COALESCE(jr.jr_nama, 'Tenaga Pengajar') END as jabatan_fungsional"),
                    DB::raw("COUNT(p.pg_id) as jumlah_orang")
                )
                ->groupBy('jabatan_fungsional')
                ->orderByRaw("CASE WHEN jabatan_fungsional = 'Tendik' THEN 1 ELSE 0 END ASC, jumlah_orang DESC")
                ->get();

            /**
             * 9. Distribusi Pangkat dan Golongan (BARU)
             * Mengambil data golongan sesuai filter unit dan memisahkan Dosen vs Tendik
             */
            $pangkatStatsRaw = DB::table('pangkat_ref as pnk')
                ->leftJoin('pangkat_trx as pnk_t', function($join) {
                    $join->on('pnk.pr_id', '=', 'pnk_t.pt_pr_id')
                         ->where('pnk_t.pt_status_aktif', 'Aktif')
                         ->where(function($q) {
                             $q->where('pnk_t.pt_isdel', 'Tidak')->orWhereNull('pnk_t.pt_isdel');
                         });
                })
                ->leftJoin('pegawai as peg', function($join) {
                    $join->on('pnk_t.pt_pg_id', '=', 'peg.pg_id')
                         ->where(function($q) {
                             $q->where('peg.pg_isdel', 'Tidak')->orWhereNull('peg.pg_isdel');
                         });
                })
                // Join ke unit trx untuk memastikan filter unitId berlaku pada data pangkat
                ->leftJoin('pegawai_unit_trx as put_p', function($join) use ($unitId) {
                    $join->on('peg.pg_id', '=', 'put_p.put_pg_id')
                         ->where(function($q) {
                             $q->where('put_p.put_status', 'Aktif')->orWhereNull('put_p.put_status');
                         });
                    if ($unitId && $unitId !== 'all') {
                        $join->where('put_p.put_unit_id', $unitId);
                    }
                })
                ->select(
                    'pnk.pr_golongan as golongan',
                    DB::raw("COUNT(DISTINCT CASE WHEN peg.pg_kategori = 'Dosen' AND put_p.put_pg_id IS NOT NULL THEN peg.pg_id END) as jumlah_dosen"),
                    DB::raw("COUNT(DISTINCT CASE WHEN peg.pg_kategori = 'Tendik' AND put_p.put_pg_id IS NOT NULL THEN peg.pg_id END) as jumlah_tendik")
                )
                ->where(function($q) {
                    $q->where('pnk.pr_isdel', 'Tidak')->orWhereNull('pnk.pr_isdel');
                })
                ->groupBy('pnk.pr_id', 'pnk.pr_golongan')
                ->orderBy('pnk.pr_id', 'asc')
                ->get();

            // Format data pangkat untuk mempermudah Chart.js di frontend
            $pangkatStats = [
                'labels' => $pangkatStatsRaw->pluck('golongan')->toArray(),
                'dosen'  => $pangkatStatsRaw->pluck('jumlah_dosen')->toArray(),
                'tendik' => $pangkatStatsRaw->pluck('jumlah_tendik')->toArray(),
            ];

            // 10. Hitung Persentase Kategori Utama
            $persenDosen = $totalPegawai > 0 ? round(($jmlDosen / $totalPegawai) * 100, 1) : 0;
            $persenTendik = $totalPegawai > 0 ? round(($jmlTendik / $totalPegawai) * 100, 1) : 0;

            // Response AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'total' => number_format($totalPegawai, 0, ',', '.'),
                    'jml_dosen' => $jmlDosen,
                    'jml_tendik' => $jmlTendik,
                    'persen_dosen' => $persenDosen . '%',
                    'persen_tendik' => $persenTendik . '%',
                    'chartData' => [$jmlDosen, $jmlTendik],
                    'genderStats' => $genderStats,
                    'eduDosen' => ['labels' => $pendidikanDosen->pluck('pdt_nama'), 'values' => $pendidikanDosen->pluck('jumlah')],
                    'eduTendik' => ['labels' => $pendidikanTendik->pluck('pdt_nama'), 'values' => $pendidikanTendik->pluck('jumlah')],
                    'agamaDosen' => $agamaDosen,
                    'agamaTendik' => $agamaTendik,
                    'usiaStats' => $usiaStats,
                    'jabatanStats' => $jabatanStats,
                    'pangkatStats' => $pangkatStats // Menambahkan data pangkat untuk AJAX
                ]);
            }

            // Return View Utama
            return view('dashboard', compact(
                'units', 'totalPegawai', 'jmlDosen', 'jmlTendik', 
                'persenDosen', 'persenTendik', 'genderStats', 
                'pendidikanDosen', 'pendidikanTendik', 
                'agamaDosen', 'agamaTendik', 'usiaStats', 
                'jabatanStats', 'pangkatStats'
            ));

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            throw $e;
        }
    }
}