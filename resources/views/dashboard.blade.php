@extends('layouts.app')

@section('content')
<style>
   
    html, body {
        height: 100%;
        margin: 0;
        overflow: hidden;
    }

    
    .dashboard-wrapper {
        display: flex;
        height: calc(100vh - 60px); 
        width: 100%;
        overflow: hidden;
    }

   
    .sidebar-sticky {
        width: 100%;
        height: 100%;
        overflow-y: auto;
        background-color: #FFD700;
        border-right: 2px solid #000;
    }

    
    .main-content-scroll {
        height: 100%;
        overflow-y: auto;
        padding: 15px;
        background-color: #f8f9fa;
        flex: 1;
    }


    .main-content-scroll::-webkit-scrollbar, .sidebar-sticky::-webkit-scrollbar {
        width: 6px;
    }
    .main-content-scroll::-webkit-scrollbar-thumb {
        background: #003366;
        border-radius: 10px;
    }
    .sidebar-sticky::-webkit-scrollbar-thumb {
        background: #FFA500;
        border-radius: 10px;
    }
</style>

<div class="container-fluid p-0">
    <div class="dashboard-wrapper">
        
        <div class="col-md-2 p-0">
            <div class="sidebar-sticky shadow">
                <div class="bg-primary text-white text-center fw-bold p-3">UNIT</div>
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

                <div class="px-2 pb-3 mt-5 text-center">
                    <img src="{{ asset('public/img/logo_unp.png') }}" width="80" class="mb-2" style="opacity: 0.9;" onerror="this.style.display='none'">
                    <p class="fw-bold mb-0" style="font-size: 12px; color: #003366;">Dashboard BI</p>
                    <small class="text-dark fw-bold" style="font-size: 9px;">SDM UNP</small>
                </div>
            </div>
        </div>

        <div class="main-content-scroll">
            
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
                                <div class="col-4 text-center">
                                    <div style="height: 90px; position: relative;"><canvas id="chartTendik"></canvas></div>
                                    <div class="mt-1 fw-bold text-success" style="font-size: 10px;">TENDIK</div>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <img src="https://cdn-icons-png.flaticon.com/512/4042/4042356.png" width="25">
                                        <img src="https://cdn-icons-png.flaticon.com/512/4140/4140047.png" width="25">
                                    </div>
                                </div>
                                <div class="col-4 text-center">
                                    <div style="height: 90px; position: relative;"><canvas id="chartDosen"></canvas></div>
                                    <div class="mt-1 fw-bold text-primary" style="font-size: 10px;">DOSEN</div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center gap-3 mt-2" style="font-size: 9px;">
                                <span class="d-flex align-items-center gap-1"><span style="width:10px;height:10px;background:#003366;display:inline-block;border-radius:2px;"></span> Laki-laki</span>
                                <span class="d-flex align-items-center gap-1"><span style="width:10px;height:10px;background:#dc3545;display:inline-block;border-radius:2px;"></span> Perempuan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-2 mb-2">
                <div class="col-md-6">
                    <div class="card shadow border-dark h-100 bg-white">
                        <div class="card-header bg-warning py-1 border-bottom border-dark text-center fw-bold small">Pendidikan Dosen</div>
                        <div class="card-body p-2" style="height: 180px;"><canvas id="eduDosenChart"></canvas></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow border-dark h-100 bg-white">
                        <div class="card-header bg-warning py-1 border-bottom border-dark text-center fw-bold small">Pendidikan Tendik</div>
                        <div class="card-body p-2" style="height: 180px;"><canvas id="eduTendikChart"></canvas></div>
                    </div>
                </div>
            </div>

            <div class="row g-2 mb-2">
                <div class="col-md-12">
                    <div class="card shadow border-dark h-100 bg-white text-center">
                        <div class="card-header bg-warning py-1 border-bottom border-dark fw-bold small">Distribusi Pegawai Berdasarkan Agama</div>
                        <div class="card-body p-2" style="height: 200px;">
                            <canvas id="agamaChart"></canvas>
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
                <div class="col-md-7">
                    <div class="card shadow border-dark h-100 bg-white">
                        <div class="card-header bg-warning py-1 border-bottom border-dark text-center fw-bold small">Distribusi Jabatan Fungsional</div>
                        <div class="card-body p-2" style="max-height: 280px; overflow-y: auto;">
                            <div class="row g-2" id="jabatanContainer">
                                @foreach($jabatanStats as $jbt)
                                <div class="col-md-6 mb-1">
                                    <div class="d-flex border border-dark rounded overflow-hidden shadow-sm" style="height: 30px;">
                                        <div class="bg-primary text-white d-flex align-items-center px-2 fw-bold flex-grow-1" style="font-size: 9px; background-color: #003366 !important;">
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
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card shadow border-dark h-100 bg-white">
                        <div class="card-header bg-warning py-1 border-bottom border-dark d-flex justify-content-between align-items-center px-3">
                            <span class="fw-bold small">Status Kepegawaian</span>
                            <div class="d-flex gap-2" style="font-size: 9px;">
                                <span class="d-flex align-items-center gap-1"><span style="width:10px;height:10px;background:#003366;display:inline-block;"></span> DS</span>
                                <span class="d-flex align-items-center gap-1"><span style="width:10px;height:10px;background:#28a745;display:inline-block;"></span> TK</span>
                            </div>
                        </div>
                        <div class="card-body p-2" style="height: 280px;"><canvas id="statusPegawaiChart"></canvas></div>
                    </div>
                </div>
            </div>

            <div class="row g-2 mb-2">
                <div class="col-md-12">
                    <div class="card shadow border-dark h-100 bg-white">
                        <div class="card-header bg-warning py-1 border-bottom border-dark d-flex justify-content-between align-items-center px-3">
                            <span class="fw-bold small">Status Keaktifan Pegawai</span>
                            <div class="d-flex gap-3" style="font-size: 10px;">
                                <span class="d-flex align-items-center gap-1"><span style="width:12px;height:12px;background:#003366;display:inline-block;border-radius:2px;"></span> DOSEN</span>
                                <span class="d-flex align-items-center gap-1"><span style="width:12px;height:12px;background:#28a745;display:inline-block;border-radius:2px;"></span> TENDIK</span>
                            </div>
                        </div>
                        <div class="card-body p-2" style="height: 250px;">
                            <canvas id="statusAktifChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-2 mb-5">
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
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<script>
    Chart.register(ChartDataLabels);
    
    
    function updateVisualChart(chartObj, labels, datasets) {
        const activeIndices = labels.map((_, i) => datasets.some(ds => ds.data[i] > 0));
        chartObj.data.labels = labels.filter((_, i) => activeIndices[i]);
        chartObj.data.datasets.forEach((ds, dsIdx) => {
            ds.data = datasets[dsIdx].data.filter((_, i) => activeIndices[i]);
        });
        chartObj.update();
    }

   
    function updatePangkatVisual(chartObj, labels, values) {
        if(!labels || !values) return;
        const filtered = labels.map((l, i) => ({ l, v: values[i] })).filter(item => item.v > 0);
        chartObj.data.labels = filtered.map(d => d.l);
        chartObj.data.datasets[0].data = filtered.map(d => d.v);
        chartObj.update();
    }

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
     
        let catChart = new Chart(document.getElementById('categoryChart'), { type: 'doughnut', data: { labels: ['Dosen', 'Tendik'], datasets: [{ data: [{{ $jmlDosen }}, {{ $jmlTendik }}], backgroundColor: ['#003366', '#28a745'] }]}, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, datalabels: { display: false }}, cutout: '70%'} });
        
        let chartT, chartD;
        const updateJK = (stats) => {
            const cfg = (d) => ({ type: 'pie', data: { labels: ['L', 'P'], datasets: [{ data: [d.L, d.P], backgroundColor: ['#003366', '#dc3545'], borderWidth: 1, borderColor: '#fff' }]}, options: { responsive: true, maintainAspectRatio: false, layout: { padding: 5 }, plugins: { legend: { display: false }, datalabels: { color: '#fff', font: { weight: 'bold', size: 10 }, formatter: (v, ctx) => { let s = ctx.dataset.data.reduce((a, b) => a + b, 0); return s > 0 ? Math.round((v/s)*100) + "%" : ""; }}}}});
            if(chartT) chartT.destroy(); if(chartD) chartD.destroy();
            chartT = new Chart(document.getElementById('chartTendik'), cfg(stats.Tendik));
            chartD = new Chart(document.getElementById('chartDosen'), cfg(stats.Dosen));
        };
        updateJK(@json($genderStats));

        let eduDosenChart = initBarChart('eduDosenChart', @json($pendidikanDosen->pluck('pdt_nama')), @json($pendidikanDosen->pluck('jumlah')), '#003366');
        let eduTendikChart = initBarChart('eduTendikChart', @json($pendidikanTendik->pluck('pdt_nama')), @json($pendidikanTendik->pluck('jumlah')), '#28a745');
        
        let agamaChart = new Chart(document.getElementById('agamaChart'), {
            type: 'bar',
            data: { labels: [], datasets: [{ label: 'Dosen', data: [], backgroundColor: '#003366' }, { label: 'Tendik', data: [], backgroundColor: '#28a745' }]},
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: true, position: 'top', labels: { boxWidth: 10, font: { size: 10 }}}, datalabels: { anchor: 'end', align: 'right', font: { size: 9, weight: 'bold' }, formatter: (v) => v > 0 ? v : '' }}, scales: { x: { beginAtZero: true }, y: { ticks: { font: { size: 10 }}}}}
        });

        let statusChart = new Chart(document.getElementById('statusPegawaiChart'), {
            type: 'bar',
            data: { labels: @json($statusPegawai['labels']), datasets: [{ label: 'Dosen', data: @json($statusPegawai['dosen']), backgroundColor: '#003366', borderRadius: 2 }, { label: 'Tendik', data: @json($statusPegawai['tendik']), backgroundColor: '#28a745', borderRadius: 2 }]},
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, datalabels: { anchor: 'center', align: 'center', color: '#fff', font: { weight: 'bold', size: 10 }, formatter: (v) => v > 0 ? v : '' }}, scales: { x: { stacked: true, display: true }, y: { stacked: true, ticks: { font: { size: 10 }}}}}
        });

        let statusAktifChart = new Chart(document.getElementById('statusAktifChart'), {
            type: 'bar',
            data: { labels: @json($statusAktif['labels']), datasets: [{ label: 'Dosen', data: @json($statusAktif['dosen']), backgroundColor: '#003366', borderRadius: 2 }, { label: 'Tendik', data: @json($statusAktif['tendik']), backgroundColor: '#28a745', borderRadius: 2 }]},
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, datalabels: { anchor: 'end', align: 'top', font: { weight: 'bold', size: 9 }, formatter: (v) => v > 0 ? v : '' }}, scales: { y: { beginAtZero: true, ticks: { font: { size: 9 }}}, x: { ticks: { font: { size: 9 }, autoSkip: false }}}}
        });

        let pnkDosenChart = initBarChart('pangkatDosenChart', @json($pangkatStats['labels']), @json($pangkatStats['dosen']), '#003366');
        let pnkTendikChart = initBarChart('pangkatTendikChart', @json($pangkatStats['labels']), @json($pangkatStats['tendik']), '#ffc107');

        const refreshAgama = (dataDosen, dataTendik) => {
            const allLabels = [...new Set([...dataDosen.labels, ...dataTendik.labels])];
            const dsDosen = allLabels.map(lbl => { const idx = dataDosen.labels.indexOf(lbl); return idx !== -1 ? dataDosen.values[idx] : 0; });
            const dsTendik = allLabels.map(lbl => { const idx = dataTendik.labels.indexOf(lbl); return idx !== -1 ? dataTendik.values[idx] : 0; });
            updateVisualChart(agamaChart, allLabels, [{data: dsDosen}, {data: dsTendik}]);
        };
        refreshAgama(@json($agamaDosen), @json($agamaTendik));

      
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
                        refreshAgama(data.agamaDosen, data.agamaTendik);
                        updateVisualChart(statusChart, data.statusPegawai.labels, [{data: data.statusPegawai.dosen}, {data: data.statusPegawai.tendik}]);
                        updateVisualChart(statusAktifChart, data.statusAktif.labels, [{data: data.statusAktif.dosen}, {data: data.statusAktif.tendik}]);
                        
                       
                        if (data.pangkatStats) {
                            updatePangkatVisual(pnkDosenChart, data.pangkatStats.labels, data.pangkatStats.dosen);
                            updatePangkatVisual(pnkTendikChart, data.pangkatStats.labels, data.pangkatStats.tendik);
                        }

                       
                        ['Dosen', 'Tendik'].forEach(k => {
                            const u = data.usiaStats[k] || { produktif: 0, non_produktif: 0, min_usia: 0, maks_usia: 0, rata_rata_usia: 0 };
                            document.getElementById(`usia_${k}_prod`).innerText = u.produktif;
                            document.getElementById(`usia_${k}_non`).innerText = u.non_produktif;
                            document.getElementById(`usia_${k}_min`).innerText = u.min_usia;
                            document.getElementById(`usia_${k}_max`).innerText = u.maks_usia;
                            document.getElementById(`usia_${k}_avg`).innerText = u.rata_rata_usia;
                        });

                        // Jabatan
                        let jHtml = ''; data.jabatanStats.forEach(j => { jHtml += `<div class="col-md-6 mb-1"><div class="d-flex border border-dark rounded overflow-hidden shadow-sm" style="height: 30px;"><div class="bg-primary text-white d-flex align-items-center px-2 fw-bold flex-grow-1" style="font-size: 9px; background-color: #003366 !important; line-height: 1.1; overflow: hidden; text-overflow: ellipsis;">${(j.jabatan_fungsional || 'TIDAK TERSEDIA').toUpperCase()}</div><div class="bg-white d-flex align-items-center justify-content-center px-2 fw-bold border-start border-dark" style="min-width: 50px; font-size: 11px;">${j.jumlah_orang}</div></div></div>`; });
                        document.getElementById('jabatanContainer').innerHTML = jHtml;
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