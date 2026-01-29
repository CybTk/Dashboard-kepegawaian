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
                    
                    <ul class="dropdown-menu w-100 shadow-lg custom-scroll animate-dropdown p-0" aria-labelledby="btnUnit" 
                        style="max-height: 400px; overflow-y: auto; background-color: #FFD700; border: 2px solid #000; border-radius: 0; z-index: 1050;">
                        
                        <li class="p-2 bg-white" style="position: sticky; top: 0; z-index: 10;">
                            <input type="text" id="unitSearch" class="form-control form-control-sm border-dark" 
                                   placeholder="Cari unit..." style="font-size: 11px; font-weight: bold;">
                        </li>

                        <div id="unitList">
                            @foreach($units as $unit)
                                <li>
                                    <a class="dropdown-item border-bottom py-2 fw-bold unit-item-link" href="#" 
                                       style="font-size: 11px; background-color: #FFA500; white-space: normal;">
                                        {{ $unit->unit_nama }}
                                    </a>
                                </li>
                            @endforeach
                        </div>

                        <li class="text-center bg-light border-top py-1" style="position: sticky; bottom: 0; border-color: #000 !important;">
                            <span style="color: red; font-size: 10px;">▼</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-10">
        <div class="row g-2">
            <div class="col-md-3">
                <div class="card p-3 shadow text-center" style="background-color: #003366; color: white; border: 2px solid #000;">
                    <h6 class="mb-2" style="font-size: 12px;">TOTAL PEGAWAI</h6>
                    <h1 class="fw-bold mb-0" style="font-size: 3rem;">{{ number_format($totalPegawai, 0, ',', '.') }}</h1>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card h-100 border-dark d-flex align-items-center justify-content-center bg-white shadow-sm">
                    <span class="text-muted small">Grafik Analisis Kepegawaian UNP</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* CSS Animasi dan Scrollbar */
    .dropdown-menu.animate-dropdown {
        display: block !important;
        visibility: hidden;
        opacity: 0;
        transform: translateY(-10px);
        transition: all 0.2s ease-in-out;
    }
    .dropdown.show .dropdown-menu.animate-dropdown {
        visibility: visible;
        opacity: 1;
        transform: translateY(0);
    }
    
    .custom-scroll::-webkit-scrollbar { width: 10px; }
    .custom-scroll::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #888; border: 1px solid #f1f1f1; }
    
    .dropdown-item:hover { background-color: #003366 !important; color: white !important; }

    /* Mencegah dropdown tertutup saat mengklik input pencarian */
    .dropdown-menu input {
        cursor: text;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('unitSearch');
        const unitItems = document.querySelectorAll('.unit-item-link');

        // Fungsi Pencarian
        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            unitItems.forEach(function(item) {
                const text = item.textContent.toLowerCase();
                // Sembunyikan atau tampilkan elemen li induknya
                item.closest('li').style.display = text.includes(filter) ? "" : "none";
            });
        });

        // Mencegah dropdown tertutup saat kolom pencarian diklik
        searchInput.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
</script>
@endsection