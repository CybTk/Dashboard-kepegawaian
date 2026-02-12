<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kepegawaian Lengkap - {{ $unitNama }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10pt; color: #333; line-height: 1.4; }
        .header { text-align: center; border-bottom: 3px solid #003366; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #003366; text-transform: uppercase; }
        .header p { margin: 5px 0; font-weight: bold; }
        
        .section-title { background-color: #003366; color: white; padding: 6px 10px; font-weight: bold; margin-top: 20px; text-transform: uppercase; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #444; }
        th { background-color: #f2f2f2; padding: 8px; font-size: 9pt; }
        td { padding: 6px; text-align: center; }
        .text-left { text-align: left; padding-left: 10px; }
        .bg-gray { background-color: #fafafa; }
        
        .footer { margin-top: 30px; text-align: right; font-size: 9pt; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN ANALISIS DATA KEPEGAWAIAN</h2>
        <h3>UNIVERSITAS NEGERI PADANG</h3>
        <p>UNIT: {{ $unitNama }}</p>
        <small>Dicetak pada: {{ date('d/m/Y H:i') }}</small>
    </div>

    <div class="section-title">1. Ringkasan Eksekutif</div>
    <table>
        <tr>
            <th width="50%">Parameter</th>
            <th>Nilai Statistik</th>
        </tr>
        <tr>
            <td class="text-left">Total Seluruh Pegawai</td>
            <td><strong>{{ number_format($totalPegawai, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td class="text-left">Jumlah Dosen</td>
            <td>{{ $jmlDosen }} ({{ $persenDosen }}%)</td>
        </tr>
        <tr>
            <td class="text-left">Jumlah Tenaga Kependidikan (Tendik)</td>
            <td>{{ $jmlTendik }} ({{ $persenTendik }}%)</td>
        </tr>
    </table>

    <div class="section-title">2. Distribusi Jenis Kelamin</div>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Laki-laki (L)</th>
                <th>Perempuan (P)</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-left">Dosen</td>
                <td>{{ $genderStats['Dosen']['L'] }}</td>
                <td>{{ $genderStats['Dosen']['P'] }}</td>
                <td>{{ $jmlDosen }}</td>
            </tr>
            <tr>
                <td class="text-left">Tendik</td>
                <td>{{ $genderStats['Tendik']['L'] }}</td>
                <td>{{ $genderStats['Tendik']['P'] }}</td>
                <td>{{ $jmlTendik }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">3. Distribusi Tingkat Pendidikan</div>
    <div style="width: 100%;">
        <div style="width: 48%; float: left;">
            <p><strong>Pendidikan Dosen:</strong></p>
            <table>
                <tr><th>Jenjang</th><th>Jml</th></tr>
                @foreach($pendidikanDosen as $edu)
                <tr><td class="text-left">{{ $edu->pdt_nama }}</td><td>{{ $edu->jumlah }}</td></tr>
                @endforeach
            </table>
        </div>
        <div style="width: 48%; float: right;">
            <p><strong>Pendidikan Tendik:</strong></p>
            <table>
                <tr><th>Jenjang</th><th>Jml</th></tr>
                @foreach($pendidikanTendik as $edu)
                <tr><td class="text-left">{{ $edu->pdt_nama }}</td><td>{{ $edu->jumlah }}</td></tr>
                @endforeach
            </table>
        </div>
    </div>
    <div style="clear: both;"></div>

    <div class="page-break"></div>

    <div class="section-title">4. Distribusi Berdasarkan Agama</div>
    <table>
        <thead>
            <tr>
                <th>Agama</th>
                <th>Dosen</th>
                <th>Tendik</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $allAgama = array_unique(array_merge($agamaDosen['labels'], $agamaTendik['labels']));
            @endphp
            @foreach($allAgama as $agm)
                @php
                    $idxD = array_search($agm, $agamaDosen['labels']);
                    $idxT = array_search($agm, $agamaTendik['labels']);
                    $valD = ($idxD !== false) ? $agamaDosen['values'][$idxD] : 0;
                    $valT = ($idxT !== false) ? $agamaTendik['values'][$idxT] : 0;
                @endphp
                <tr>
                    <td class="text-left">{{ $agm }}</td>
                    <td>{{ $valD }}</td>
                    <td>{{ $valT }}</td>
                    <td><strong>{{ $valD + $valT }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">5. Analisis Usia Pegawai</div>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Produktif (15-64)</th>
                <th>Non-Produktif (>64)</th>
                <th>Usia Min</th>
                <th>Usia Max</th>
                <th>Rata-rata Usia</th>
            </tr>
        </thead>
        <tbody>
            @foreach(['Dosen', 'Tendik'] as $kat)
            <tr>
                <td class="text-left">{{ $kat }}</td>
                <td>{{ $usiaStats[$kat]->produktif ?? 0 }}</td>
                <td>{{ $usiaStats[$kat]->non_produktif ?? 0 }}</td>
                <td>{{ $usiaStats[$kat]->min_usia ?? 0 }}</td>
                <td>{{ $usiaStats[$kat]->maks_usia ?? 0 }}</td>
                <td>{{ $usiaStats[$kat]->rata_rata_usia ?? 0 }} Tahun</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">6. Distribusi Jabatan Fungsional</div>
    <table>
        <thead>
            <tr>
                <th>Nama Jabatan Fungsional</th>
                <th>Jumlah Orang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jabatanStats as $jbt)
            <tr>
                <td class="text-left">{{ strtoupper($jbt->jabatan_fungsional) }}</td>
                <td>{{ $jbt->jumlah_orang }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <div class="section-title">7. Pangkat dan Golongan</div>
    <table>
        <thead>
            <tr>
                <th>Golongan</th>
                <th>Dosen</th>
                <th>Tendik</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pangkatStats['labels'] as $idx => $gol)
            <tr>
                <td class="text-left">{{ $gol }}</td>
                <td>{{ $pangkatStats['dosen'][$idx] }}</td>
                <td>{{ $pangkatStats['tendik'][$idx] }}</td>
                <td><strong>{{ $pangkatStats['dosen'][$idx] + $pangkatStats['tendik'][$idx] }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">8. Status Keaktifan Pegawai</div>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Dosen</th>
                <th>Tendik</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statusAktif['labels'] as $idx => $status)
            <tr>
                <td class="text-left">{{ $status }}</td>
                <td>{{ $statusAktif['dosen'][$idx] }}</td>
                <td>{{ $statusAktif['tendik'][$idx] }}</td>
                <td><strong>{{ $statusAktif['dosen'][$idx] + $statusAktif['tendik'][$idx] }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini dihasilkan secara otomatis oleh Sistem BI Kepegawaian UNP pada {{ date('d F Y, H:i') }} WIB.</p>
    </div>
</body>
</html>