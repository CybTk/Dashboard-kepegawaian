@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-2 p-1">
        <div class="sidebar-container shadow" style="background-color: #FFD700; border: 2px solid #000; min-height: 85vh;">
            <div class="bg-primary text-white text-center fw-bold p-2">UNIT</div>
            <div class="p-2">
                <div class="dropdown">
                    <button class="btn dropdown-toggle w-100 fw-bold d-flex justify-content-between align-items-center shadow-sm" 
                            type="button" id="btnUnit" data-bs-toggle="dropdown" aria-expanded="false"
                            style="background-color: #FFA500; border: 1px solid #000; font-size: 11px;">
                        PILIH UNIT
                    </button>
                    <ul class="dropdown-menu w-100 shadow-lg custom-scroll animate-dropdown p-0" style="max-height: 400px; overflow-y: auto; background-color: #FFD700; border: 2px solid #000; z-index: 1050;">
                        <li class="p-2 bg-white" style="position: sticky; top: 0; z-index: 10;">
                            <input type="text" id="unitSearch" class="form-control form-control-sm border-dark" placeholder="Cari unit..." style="font-size: 11px;">
                        </li>
                        <div id="unitList">
                            <li><a class="dropdown-item border-bottom py-2 fw-bold unit-item-link" href="#" data-id="all" style="font-size: 11px; background-color: #FFA500;">UNIVERSITAS (SEMUA)</a></li>
                            @foreach($units as $unit)
                                <li><a class="dropdown-item border-bottom py-2 fw-bold unit-item-link" href="#" data-id="{{ $unit->unit_id }}" style="font-size: 11px; background-color: #FFA500; white-space: normal;">{{ $unit->unit_nama }}</a></li>
                            @endforeach
                        </div>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-10">
        <div class="row g-2 mb-2">
            <div class="col-md-3">
                <div class="card p-3 shadow text-center h-100" style="background-color: #003366; color: white; border: 2px solid #000;">
                    <h6 class="mb-2" style="font-size: 12px;">TOTAL PEGAWAI</h6>
                    <h1 id="totalPegawaiText" class="fw-bold mb-0" style="font-size: 3rem;">{{ number_format($totalPegawai, 0, ',', '.') }}</h1>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3 shadow border-dark h-100 bg-white text-center">
                    <h6 class="fw-bold mb-3" style="font-size: 11px; color: #003366;">PERSENTASE DOSEN & TENDIK</h6>
                    <div class="row align-items-center g-0">
                        <div class="col-6 text-center">
                            <div style="height: 100px; width: 100px; margin: 0 auto;"><canvas id="categoryChart"></canvas></div>
                        </div>
                        <div class="col-6 text-start ps-2">
                            <div class="mb-2">
                                <span class="fw-bold d-block" style="font-size: 10px; color: #003366;">DOSEN</span>
                                <h4 id="valDosen" class="fw-bold mb-0 text-primary">{{ $jmlDosen }}</h4>
                                <small id="pctDosen" class="text-muted" style="font-size: 10px;">{{ $persenDosen }}%</small>
                            </div>
                            <div>
                                <span class="fw-bold d-block" style="font-size: 10px; color: #28a745;">TENDIK</span>
                                <h4 id="valTendik" class="fw-bold mb-0 text-success">{{ $jmlTendik }}</h4>
                                <small id="pctTendik" class="text-muted" style="font-size: 10px;">{{ $persenTendik }}%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card h-100 border-dark bg-white shadow-sm overflow-hidden text-center">
                    <div class="card-header bg-warning py-1 border-bottom border-dark">
                        <span class="fw-bold" style="font-size: 11px;">Persentase Pegawai Berdasarkan Jenis Kelamin</span>
                    </div>
                    <div class="card-body p-2">
                        <div class="row align-items-center g-0">
                            <div class="col-4 text-center"><div style="height: 80px;"><canvas id="chartTendik"></canvas></div><div class="mt-1 fw-bold" style="font-size: 9px;">Tendik</div></div>
                            <div class="col-4 text-center"><div class="d-flex justify-content-center gap-1"><img src="https://cdn-icons-png.flaticon.com/512/4042/4042356.png" width="30"><img src="https://cdn-icons-png.flaticon.com/512/4140/4140047.png" width="30"></div></div>
                            <div class="col-4 text-center"><div style="height: 80px;"><canvas id="chartDosen"></canvas></div><div class="mt-1 fw-bold" style="font-size: 9px;">Dosen</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-2 mb-2">
            <div class="col-md-6">
                <div class="card shadow border-dark h-100 bg-white">
                    <div class="card-header bg-warning py-1 border-bottom border-dark text-center fw-bold small">Pendidikan Dosen</div>
                    <div class="card-body p-2" style="height: 150px;"><canvas id="eduDosenChart"></canvas></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow border-dark h-100 bg-white">
                    <div class="card-header bg-warning py-1 border-bottom border-dark text-center fw-bold small">Pendidikan Tendik</div>
                    <div class="card-body p-2" style="height: 150px;"><canvas id="eduTendikChart"></canvas></div>
                </div>
            </div>
        </div>

        <div class="row g-2 mb-2">
            <div class="col-md-12">
                <div class="card shadow border-dark h-100 bg-white text-center">
                    <div class="card-header bg-warning py-1 border-bottom border-dark fw-bold small">Distribusi Pegawai Berdasarkan Agama</div>
                    <div class="card-body p-2">
                        <div class="row align-items-center g-0">
                            <div class="col-5 border-end"><div style="height: 130px;"><canvas id="agamaDosenChart"></canvas></div><div class="mt-1 fw-bold small">Dosen</div></div>
                            <div class="col-5"><div style="height: 130px;"><canvas id="agamaTendikChart"></canvas></div><div class="mt-1 fw-bold small">Tendik</div></div>
                            <div class="col-2 text-start ps-3" id="agamaLegendContainer" style="font-size: 9px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-2 mb-2">
            @foreach(['Dosen', 'Tendik'] as $kat)
            <div class="col-md-6">
                <div class="card shadow border-dark h-100 bg-white">
                    <div class="card-header bg-primary text-white py-1 border-bottom border-dark text-center fw-bold small">Analisis Usia {{ $kat }}</div>
                    <div class="card-body p-2">
                        <div class="row text-center mb-2">
                            <div class="col-6 border-end">
                                <span class="fw-bold text-success" style="font-size: 9px;">PRODUKTIF</span>
                                <h4 id="usia_{{$kat}}_prod" class="fw-bold mb-0 text-success">{{ $usiaStats[$kat]->produktif ?? 0 }}</h4>
                            </div>
                            <div class="col-6">
                                <span class="fw-bold text-danger" style="font-size: 9px;">NON-PRODUKTIF</span>
                                <h4 id="usia_{{$kat}}_non" class="fw-bold mb-0 text-danger">{{ $usiaStats[$kat]->non_produktif ?? 0 }}</h4>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between px-3 border-top pt-2 small">
                            <span>Min: <b id="usia_{{$kat}}_min">{{ $usiaStats[$kat]->min_usia ?? 0 }}</b> thn</span>
                            <span>Max: <b id="usia_{{$kat}}_max">{{ $usiaStats[$kat]->maks_usia ?? 0 }}</b> thn</span>
                            <span>Rata: <b id="usia_{{$kat}}_avg">{{ $usiaStats[$kat]->rata_rata_usia ?? 0 }}</b> thn</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row g-2 mb-2">
            <div class="col-md-9">
                <div class="card shadow border-dark h-100 bg-white">
                    <div class="card-header bg-warning py-1 border-bottom border-dark text-center fw-bold small">Distribusi Jabatan Fungsional</div>
                    <div class="card-body p-2" style="max-height: 280px; overflow-y: auto; overflow-x: hidden;">
                        <div class="row g-2" id="jabatanContainer">
                            @foreach($jabatanStats as $jbt)
                            <div class="col-md-6 mb-1">
                                <div class="d-flex border border-dark rounded overflow-hidden shadow-sm" style="height: 30px;">
                                    <div class="bg-primary text-white d-flex align-items-center px-2 fw-bold flex-grow-1" 
                                         style="font-size: 9px; background-color: #003366 !important; line-height: 1.1; overflow: hidden; text-overflow: ellipsis;">
                                        {{ strtoupper($jbt->jabatan_fungsional ?? 'TIDAK TERSEDIA') }}
                                    </div>
                                    <div class="bg-white d-flex align-items-center justify-content-center px-2 fw-bold border-start border-dark" style="min-width: 50px; font-size: 11px;">
                                        {{ $jbt->jumlah_orang }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer bg-light py-0 text-center border-top border-dark"><small style="font-size: 8px;">Scroll untuk melihat jabatan lainnya</small></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow border-dark h-100" style="background-color: #f8f9fa; border-style: dashed !important;">
                    <div class="card-body d-flex align-items-center justify-content-center text-center p-2">
                        <div class="text-muted"><i class="fas fa-info-circle mb-2 d-block" style="font-size: 20px; opacity: 0.3;"></i><span style="font-size: 10px; font-style: italic;">Area Statistik Tambahan</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-2">
            <div class="col-md-6">
                <div class="card shadow border-dark h-100 bg-white">
                    <div class="card-header bg-primary text-white py-1 border-bottom border-dark text-center fw-bold small">Pangkat dan Golongan Dosen</div>
                    <div class="card-body p-2" style="height: 350px;"><canvas id="pangkatDosenChart"></canvas></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow border-dark h-100 bg-white">
                    <div class="card-header bg-primary text-white py-1 border-bottom border-dark text-center fw-bold small">Pangkat dan Golongan Tendik</div>
                    <div class="card-body p-2" style="height: 350px;"><canvas id="pangkatTendikChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<script>
    Chart.register(ChartDataLabels);
    const AGAMA_COLORS = {'Islam': '#003366', 'Kristen': '#ffc107', 'Katolik': '#dc3545', 'Hindu': '#17a2b8', 'Budha': '#28a745', 'Konghucu': '#6c757d'};

    // Fungsi Global untuk Filter Angka 0 di Grafik Pangkat
    function updatePangkatVisual(chartObj, labels, values) {
        const filtered = labels.map((l, i) => ({ l, v: values[i] })).filter(item => item.v > 0);
        chartObj.data.labels = filtered.map(d => d.l);
        chartObj.data.datasets[0].data = filtered.map(d => d.v);
        chartObj.update();
    }

    // Fungsi Helper Inisialisasi Bar Chart
    const initBarChart = (id, lbl, val, color) => {
        const filtered = lbl.map((l, i) => ({ l, v: val[i] })).filter(item => item.v > 0);
        return new Chart(document.getElementById(id), { 
            type: 'bar', 
            data: { labels: filtered.map(d => d.l), datasets: [{ data: filtered.map(d => d.v), backgroundColor: color, borderRadius: 4, barThickness: 15 }]}, 
            options: { 
                indexAxis: 'y', responsive: true, maintainAspectRatio: false, 
                plugins: { legend: { display: false }, datalabels: { anchor: 'end', align: 'right', color: '#000', font: { weight: 'bold', size: 9 }}}, 
                scales: { x: { display: true, beginAtZero: true }, y: { ticks: { font: { size: 9 }, autoSkip: false }}} 
            } 
        });
    };

    document.addEventListener('DOMContentLoaded', function() {
        // --- INISIALISASI ---
        let catChart = new Chart(document.getElementById('categoryChart'), { type: 'doughnut', data: { labels: ['Dosen', 'Tendik'], datasets: [{ data: [{{ $jmlDosen }}, {{ $jmlTendik }}], backgroundColor: ['#003366', '#28a745'] }]}, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, datalabels: { display: false }}, cutout: '70%'} });
        
        let chartT, chartD;
        const updateJK = (stats) => {
            const cfg = (d) => ({ type: 'pie', data: { labels: ['L', 'P'], datasets: [{ data: [d.L, d.P], backgroundColor: ['#003366', '#dc3545'] }]}, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, datalabels: { color: '#fff', font: { weight: 'bold', size: 9 }, formatter: (v, ctx) => { let s = ctx.dataset.data.reduce((a, b) => a + b, 0); return s > 0 ? Math.round((v/s)*100) + "%" : ""; }}}}});
            if(chartT) chartT.destroy(); if(chartD) chartD.destroy();
            chartT = new Chart(document.getElementById('chartTendik'), cfg(stats.Tendik));
            chartD = new Chart(document.getElementById('chartDosen'), cfg(stats.Dosen));
        };
        updateJK(@json($genderStats));

        let eduDosenChart = initBarChart('eduDosenChart', @json($pendidikanDosen->pluck('pdt_nama')), @json($pendidikanDosen->pluck('jumlah')), '#003366');
        let eduTendikChart = initBarChart('eduTendikChart', @json($pendidikanTendik->pluck('pdt_nama')), @json($pendidikanTendik->pluck('jumlah')), '#28a745');
        let pnkDosenChart = initBarChart('pangkatDosenChart', @json($pangkatStats['labels']), @json($pangkatStats['dosen']), '#003366');
        let pnkTendikChart = initBarChart('pangkatTendikChart', @json($pangkatStats['labels']), @json($pangkatStats['tendik']), '#ffc107');

        let agD, agT;
        const updateAg = (dLabels, dValues, tLabels, tValues) => {
            const allL = [...new Set([...dLabels, ...tLabels])];
            const cfg = (l, v) => ({ type: 'pie', data: { labels: l, datasets: [{ data: v, backgroundColor: l.map(x => AGAMA_COLORS[x] || '#000') }]}, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, datalabels: { color: '#fff', font: { weight: 'bold', size: 8 }, formatter: (v, ctx) => { let s = ctx.dataset.data.reduce((a, b) => a + b, 0); let p = Math.round((v/s)*100); return p > 10 ? p + "%" : ""; }}}}});
            if(agD) agD.destroy(); if(agT) agT.destroy();
            agD = new Chart(document.getElementById('agamaDosenChart'), cfg(dLabels, dValues));
            agT = new Chart(document.getElementById('agamaTendikChart'), cfg(tLabels, tValues));
            let html = '<div class="fw-bold mb-1 border-bottom">Agama:</div>';
            allL.forEach(l => { html += `<div class="d-flex align-items-center gap-1 mb-1"><span style="display:inline-block;width:10px;height:10px;background:${AGAMA_COLORS[l] || '#000'}"></span><span>${l}</span></div>`; });
            document.getElementById('agamaLegendContainer').innerHTML = html;
        };
        updateAg(@json($agamaDosen['labels']), @json($agamaDosen['values']), @json($agamaTendik['labels']), @json($agamaTendik['values']));

        // --- AJAX SYNC ---
        document.querySelectorAll('.unit-item-link').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const unitId = this.getAttribute('data-id');
                document.getElementById('btnUnit').innerText = this.innerText;
                fetch(`{{ route('dashboard') }}?unit_id=${unitId}`, { headers: {'X-Requested-With': 'XMLHttpRequest'}})
                .then(res => res.json()).then(data => {
                    if(data.success) {
                        document.getElementById('totalPegawaiText').innerText = data.total;
                        document.getElementById('valDosen').innerText = data.jml_dosen;
                        document.getElementById('valTendik').innerText = data.jml_tendik;
                        document.getElementById('pctDosen').innerText = data.persen_dosen;
                        document.getElementById('pctTendik').innerText = data.persen_tendik;
                        catChart.data.datasets[0].data = data.chartData; catChart.update();
                        updateJK(data.genderStats);
                        eduDosenChart.data.labels = data.eduDosen.labels; eduDosenChart.data.datasets[0].data = data.eduDosen.values; eduDosenChart.update();
                        eduTendikChart.data.labels = data.eduTendik.labels; eduTendikChart.data.datasets[0].data = data.eduTendik.values; eduTendikChart.update();
                        updateAg(data.agamaDosen.labels, data.agamaDosen.values, data.agamaTendik.labels, data.agamaTendik.values);
                        ['Dosen', 'Tendik'].forEach(k => {
                            const u = data.usiaStats[k] || { produktif: 0, non_produktif: 0, min_usia: 0, maks_usia: 0, rata_rata_usia: 0 };
                            document.getElementById(`usia_${k}_prod`).innerText = u.produktif;
                            document.getElementById(`usia_${k}_non`).innerText = u.non_produktif;
                            document.getElementById(`usia_${k}_min`).innerText = u.min_usia;
                            document.getElementById(`usia_${k}_max`).innerText = u.maks_usia;
                            document.getElementById(`usia_${k}_avg`).innerText = u.rata_rata_usia;
                        });
                        let jHtml = ''; data.jabatanStats.forEach(j => { jHtml += `<div class="col-md-6 mb-1"><div class="d-flex border border-dark rounded overflow-hidden shadow-sm" style="height: 30px;"><div class="bg-primary text-white d-flex align-items-center px-2 fw-bold flex-grow-1" style="font-size: 9px; background-color: #003366 !important; line-height: 1.1; overflow: hidden; text-overflow: ellipsis;">${(j.jabatan_fungsional || 'TIDAK TERSEDIA').toUpperCase()}</div><div class="bg-white d-flex align-items-center justify-content-center px-2 fw-bold border-start border-dark" style="min-width: 50px; font-size: 11px;">${j.jumlah_orang}</div></div></div>`; });
                        document.getElementById('jabatanContainer').innerHTML = jHtml;
                        
                        updatePangkatVisual(pnkDosenChart, data.pangkatStats.labels, data.pangkatStats.dosen);
                        updatePangkatVisual(pnkTendikChart, data.pangkatStats.labels, data.pangkatStats.tendik);
                    }
                });
            });
        });

        document.getElementById('unitSearch').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            document.querySelectorAll('.unit-item-link').forEach(item => { item.closest('li').style.display = item.textContent.toLowerCase().includes(filter) ? "" : "none"; });
        });
    });
</script>
@endsection