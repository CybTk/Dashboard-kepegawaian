<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan library dompdf sudah diinstal

class KepegawaianController extends Controller
{
    /**
     * Mengambil data dashboard berdasarkan request filter unit.
     * Logika dipisah agar bisa digunakan oleh index() dan exportPdf().
     */
    private function getDashboardData(Request $request)
    {
        $unitId = $request->query('unit_id');

        $units = DB::table('unit')
            ->select('unit_id', 'unit_nama')
            ->orderBy('unit_nama', 'asc')
            ->get();

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

        if ($unitId && $unitId !== 'all') {
            $baseQuery->where('put.put_unit_id', $unitId);
        }

        $totalPegawai = (clone $baseQuery)->count();

        $distribusi = (clone $baseQuery)
            ->select('p.pg_kategori', DB::raw('COUNT(p.pg_id) as jumlah'))
            ->groupBy('p.pg_kategori')
            ->get()
            ->pluck('jumlah', 'pg_kategori');

        $jmlDosen = $distribusi['Dosen'] ?? 0;
        $jmlTendik = $distribusi['Tendik'] ?? 0;

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

        $getPendidikan = function($kategori) use ($baseQuery) {
            return (clone $baseQuery)
                ->join('pendidikan as pd', 'p.pg_id', '=', 'pd.pd_pg_id')
                ->join('pendidikan_tingkat as pt', 'pd.pd_pdt_id', '=', 'pt.pdt_id')
                ->where('p.pg_kategori', $kategori)
                ->where(function($q) { 
                    $q->where('pd.pd_status_sk', 'Aktif')->orWhereNull('pd.pd_status_sk'); 
                })
                ->select('pt.pdt_nama', DB::raw('COUNT(p.pg_id) as jumlah'))
                ->groupBy('pt.pdt_id', 'pt.pdt_nama')
                ->orderBy('pt.pdt_id', 'asc')->get();
        };

        $pendidikanDosen = $getPendidikan('Dosen');
        $pendidikanTendik = $getPendidikan('Tendik');

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

        $usiaStats = (clone $baseQuery)
            ->select(
                'p.pg_kategori',
                DB::raw("SUM(CASE WHEN TIMESTAMPDIFF(YEAR, p.pg_tgllahir, CURDATE()) BETWEEN 15 AND 64 THEN 1 ELSE 0 END) as produktif"),
                DB::raw("SUM(CASE WHEN TIMESTAMPDIFF(YEAR, p.pg_tgllahir, CURDATE()) > 64 THEN 1 ELSE 0 END) as non_produktif"),
                DB::raw("MAX(TIMESTAMPDIFF(YEAR, p.pg_tgllahir, CURDATE())) as maks_usia"),
                DB::raw("MIN(TIMESTAMPDIFF(YEAR, p.pg_tgllahir, CURDATE())) as min_usia"),
                DB::raw("ROUND(AVG(TIMESTAMPDIFF(YEAR, p.pg_tgllahir, CURDATE())), 0) as rata_rata_usia")
            )
            ->groupBy('p.pg_kategori')
            ->get()
            ->keyBy('pg_kategori');

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

        $pangkatStatsRaw = DB::table('pangkat_ref as pnk')
            ->leftJoin('pangkat_trx as pnk_t', function($join) {
                $join->on('pnk.pr_id', '=', 'pnk_t.pt_pr_id')
                     ->where('pnk_t.pt_status_aktif', 'Aktif')
                     ->where(function($q) { $q->where('pnk_t.pt_isdel', 'Tidak')->orWhereNull('pnk_t.pt_isdel'); });
            })
            ->leftJoin('pegawai as peg', function($join) {
                $join->on('pnk_t.pt_pg_id', '=', 'peg.pg_id')
                     ->where(function($q) { $q->where('peg.pg_isdel', 'Tidak')->orWhereNull('peg.pg_isdel'); });
            })
            ->leftJoin('pegawai_unit_trx as put_p', function($join) use ($unitId) {
                $join->on('peg.pg_id', '=', 'put_p.put_pg_id')
                     ->where(function($q) { $q->where('put_p.put_status', 'Aktif')->orWhereNull('put_p.put_status'); });
                if ($unitId && $unitId !== 'all') { $join->where('put_p.put_unit_id', $unitId); }
            })
            ->select(
                'pnk.pr_golongan as golongan',
                DB::raw("COUNT(DISTINCT CASE WHEN peg.pg_kategori = 'Dosen' AND put_p.put_pg_id IS NOT NULL THEN peg.pg_id END) as jumlah_dosen"),
                DB::raw("COUNT(DISTINCT CASE WHEN peg.pg_kategori = 'Tendik' AND put_p.put_pg_id IS NOT NULL THEN peg.pg_id END) as jumlah_tendik")
            )
            ->where(function($q) { $q->where('pnk.pr_isdel', 'Tidak')->orWhereNull('pnk.pr_isdel'); })
            ->groupBy('pnk.pr_id', 'pnk.pr_golongan')
            ->orderBy('pnk.pr_id', 'asc')
            ->get();

        $pangkatStats = [
            'labels' => $pangkatStatsRaw->pluck('golongan')->toArray(),
            'dosen'  => $pangkatStatsRaw->pluck('jumlah_dosen')->toArray(),
            'tendik' => $pangkatStatsRaw->pluck('jumlah_tendik')->toArray(),
        ];

        $statusPegawaiRaw = DB::table('jenis_pegawai_ref as jref')
            ->leftJoin('jenis_pegawai_trx as jtrx', function($join) {
                $join->on('jtrx.jpt_jpr_id', '=', 'jref.jpr_id')
                     ->where(function($q) { $q->where('jtrx.jpt_isdel', 'Tidak')->orWhereNull('jtrx.jpt_isdel'); });
            })
            ->leftJoin('pegawai as peg', function($join) {
                $join->on('jtrx.jpt_pg_id', '=', 'peg.pg_id')
                     ->where(function($q) { $q->where('peg.pg_isdel', 'Tidak')->orWhereNull('peg.pg_isdel'); });
            })
            ->leftJoin('pegawai_unit_trx as put_s', function($join) use ($unitId) {
                $join->on('peg.pg_id', '=', 'put_s.put_pg_id')
                     ->where(function($q) { $q->where('put_s.put_status', 'Aktif')->orWhereNull('put_s.put_status'); });
                if ($unitId && $unitId !== 'all') { $join->where('put_s.put_unit_id', $unitId); }
            })
            ->select(
                DB::raw("COALESCE(jref.jpr_nama, 'Lainnya') as status_nama"),
                DB::raw("COUNT(DISTINCT CASE WHEN peg.pg_kategori = 'Dosen' AND put_s.put_pg_id IS NOT NULL THEN peg.pg_id END) as jml_dosen"),
                DB::raw("COUNT(DISTINCT CASE WHEN peg.pg_kategori = 'Tendik' AND put_s.put_pg_id IS NOT NULL THEN peg.pg_id END) as jml_tendik")
            )
            ->where(function($q) { $q->where('jref.jpr_isdel', 'Tidak')->orWhereNull('jref.jpr_isdel'); })
            ->groupBy('jref.jpr_id', 'jref.jpr_nama')
            ->orderByRaw("(COUNT(DISTINCT CASE WHEN peg.pg_kategori = 'Dosen' AND put_s.put_pg_id IS NOT NULL THEN peg.pg_id END) + COUNT(DISTINCT CASE WHEN peg.pg_kategori = 'Tendik' AND put_s.put_pg_id IS NOT NULL THEN peg.pg_id END)) DESC")
            ->get();

        $statusPegawai = [
            'labels' => $statusPegawaiRaw->pluck('status_nama')->toArray(),
            'dosen'  => $statusPegawaiRaw->pluck('jml_dosen')->toArray(),
            'tendik' => $statusPegawaiRaw->pluck('jml_tendik')->toArray(),
        ];

        $statusAktifRaw = DB::table('status_pegawai_ref as sref')
            ->leftJoin('status_pegawai_trx as strx', function($join) {
                $join->on('sref.sp_id', '=', 'strx.spt_sp_id')
                     ->where(function($q) { $q->where('strx.spt_isdel', 'Tidak')->orWhereNull('strx.spt_isdel'); });
            })
            ->leftJoin('pegawai as peg', function($join) {
                $join->on('strx.spt_pg_id', '=', 'peg.pg_id')
                     ->where(function($q) { $q->where('peg.pg_isdel', 'Tidak')->orWhereNull('peg.pg_isdel'); });
            })
            ->leftJoin('pegawai_unit_trx as put_aktif', function($join) use ($unitId) {
                $join->on('peg.pg_id', '=', 'put_aktif.put_pg_id')
                     ->where(function($q) { $q->where('put_aktif.put_isdel', 'Tidak')->orWhereNull('put_aktif.put_isdel'); });
                if ($unitId && $unitId !== 'all') { $join->where('put_aktif.put_unit_id', $unitId); }
            })
            ->select(
                'sref.sp_nama as status_nama',
                DB::raw("COUNT(DISTINCT CASE WHEN peg.pg_kategori = 'Dosen' AND put_aktif.put_pg_id IS NOT NULL THEN peg.pg_id END) as jml_dosen"),
                DB::raw("COUNT(DISTINCT CASE WHEN peg.pg_kategori = 'Tendik' AND put_aktif.put_pg_id IS NOT NULL THEN peg.pg_id END) as jml_tendik")
            )
            ->where(function($q) { $q->where('sref.sp_isdel', 'Tidak')->orWhereNull('sref.sp_isdel'); })
            ->groupBy('sref.sp_id', 'sref.sp_nama')
            ->orderBy('sref.sp_id', 'asc')
            ->get();

        $statusAktif = [
            'labels' => $statusAktifRaw->pluck('status_nama')->toArray(),
            'dosen'  => $statusAktifRaw->pluck('jml_dosen')->toArray(),
            'tendik' => $statusAktifRaw->pluck('jml_tendik')->toArray(),
        ];

        $persenDosen = $totalPegawai > 0 ? round(($jmlDosen / $totalPegawai) * 100, 1) : 0;
        $persenTendik = $totalPegawai > 0 ? round(($jmlTendik / $totalPegawai) * 100, 1) : 0;

        return compact(
            'units', 'totalPegawai', 'jmlDosen', 'jmlTendik', 
            'persenDosen', 'persenTendik', 'genderStats', 
            'pendidikanDosen', 'pendidikanTendik', 
            'agamaDosen', 'agamaTendik', 'usiaStats', 
            'jabatanStats', 'pangkatStats', 'statusPegawai', 'statusAktif', 'unitId'
        );
    }

    public function index(Request $request)
    {
        try {
            $data = $this->getDashboardData($request);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'total' => number_format($data['totalPegawai'], 0, ',', '.'),
                    'jml_dosen' => $data['jmlDosen'],
                    'jml_tendik' => $data['jmlTendik'],
                    'persen_dosen' => $data['persenDosen'] . '%',
                    'persen_tendik' => $data['persenTendik'] . '%',
                    'chartData' => [$data['jmlDosen'], $data['jmlTendik']],
                    'genderStats' => $data['genderStats'],
                    'eduDosen' => ['labels' => $data['pendidikanDosen']->pluck('pdt_nama'), 'values' => $data['pendidikanDosen']->pluck('jumlah')],
                    'eduTendik' => ['labels' => $data['pendidikanTendik']->pluck('pdt_nama'), 'values' => $data['pendidikanTendik']->pluck('jumlah')],
                    'agamaDosen' => $data['agamaDosen'],
                    'agamaTendik' => $data['agamaTendik'],
                    'usiaStats' => $data['usiaStats'],
                    'jabatanStats' => $data['jabatanStats'],
                    'pangkatStats' => $data['pangkatStats'],
                    'statusPegawai' => $data['statusPegawai'],
                    'statusAktif' => $data['statusAktif'] 
                ]);
            }

            return view('dashboard', $data);

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            throw $e;
        }
    }

    /**
     * Fitur Export ke PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $data = $this->getDashboardData($request);
            
            // Nama unit untuk judul laporan
            $unitNama = 'UNIVERSITAS (SEMUA)';
            if ($data['unitId'] && $data['unitId'] !== 'all') {
                $unit = DB::table('unit')->where('unit_id', $data['unitId'])->first();
                $unitNama = $unit ? strtoupper($unit->unit_nama) : $unitNama;
            }
            $data['unitNama'] = $unitNama;

            $pdf = Pdf::loadView('exports.kepegawaian_pdf', $data);
            
            // Set ukuran kertas A4 Portrait
            $pdf->setPaper('a4', 'portrait');

            return $pdf->download('Laporan_Kepegawaian_' . str_replace(' ', '_', $unitNama) . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
}