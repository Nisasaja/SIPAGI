@extends('partial.main')

@section('body')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-12 text-center">
            <h1 class="fw-bold text-primary">Dashboard</h1>
            <p class="text-muted">Pantau statistik status gizi balita dengan visualisasi yang informatif.</p>
        </div>
    </div>

    <!-- Statistik Kartu -->
    <div class="row g-3">
        <!-- Kartu Jumlah Balita -->
        <div class="col-md-4">
            <div class="card shadow border-0 rounded-3 p-3">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-people-fill fs-2 text-primary me-3"></i>
                    <div>
                        <h5 class="text-primary fw-semibold mb-1">Jumlah Balita</h5>
                        <h3 class="fw-bold mb-0">{{ $jumlahBalita }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu Balita TB Normal -->
        <div class="col-md-4">
            <div class="card shadow border-0 rounded-3 p-3">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-emoji-smile-fill fs-2 text-success me-3"></i>
                    <div>
                        <h5 class="text-success fw-semibold mb-1">Balita TB Normal</h5>
                        <h3 class="fw-bold mb-0">{{ $jumlahBalitaLulus }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu Teridentifikasi Stunting -->
        <div class="col-md-4">
            <div class="card shadow border-0 rounded-3 p-3">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-2 text-danger me-3"></i>
                    <div>
                        <h5 class="text-danger fw-semibold mb-1">Teridentifikasi Stunting</h5>
                        <h3 class="fw-bold mb-0">{{ $balitaTeridentifikasiStunting }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik -->
    <div class="row mt-5">
        <!-- Chart BB/U -->
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white text-center fw-bold">
                    Grafik Berat Badan Berdasarkan Umur
                </div>
                <div class="card-body">
                    <canvas id="chartBBU"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart TB/U -->
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white text-center fw-bold">
                    Grafik Tinggi Badan Berdasarkan Umur
                </div>
                <div class="card-body">
                    <canvas id="chartTBU"></canvas>
                </div>
            </div>
        </div>
    </div>   
</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data for BB/U Chart
        const ctxBBU = document.getElementById('chartBBU').getContext('2d');
        new Chart(ctxBBU, {
            type: 'bar',
            data: {
                labels: @json($tahun),
                datasets: [
                    {
                        label: 'Normal',
                        data: @json($bb_u_normal),
                        backgroundColor: 'rgba(75, 192, 192, 0.8)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        borderRadius: 10,
                    },
                    {
                        label: 'Gizi Kurang',
                        data: @json($bb_u_gizi_kurang),
                        backgroundColor: 'rgba(255, 159, 64, 0.8)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 2,
                        borderRadius: 10,
                    },
                    {
                        label: 'Gizi Buruk',
                        data: @json($bb_u_gizi_buruk),
                        backgroundColor: 'rgba(255, 99, 132, 0.8)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        borderRadius: 10,
                    },
                    {
                        label: 'Obesitas',
                        data: @json($bb_u_obesitas),
                        backgroundColor: 'rgba(255, 206, 86, 0.8)',
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 2,
                        borderRadius: 10,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: 'Grafik BB/U per Tahun' },
                },
                scales: {
                    y: { beginAtZero: true },
                },
            },
        });

        // Data for TB/U Chart
        const ctxTBU = document.getElementById('chartTBU').getContext('2d');
        new Chart(ctxTBU, {
            type: 'bar',
            data: {
                labels: @json($tahun),
                datasets: [
                    {
                        label: 'Normal',
                        data: @json($tb_u_normal),
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        borderRadius: 10,
                    },
                    {
                        label: 'Sangat Pendek',
                        data: @json($tb_u_stunting),
                        backgroundColor: 'rgba(255, 99, 132, 0.8)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        borderRadius: 10,
                    },
                    {
                        label: 'Pendek',
                        data: @json($tb_u_pendek),
                        backgroundColor: 'rgba(153, 102, 255, 0.8)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 2,
                        borderRadius: 10,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: 'Grafik TB/U per Tahun' },
                },
                scales: {
                    y: { beginAtZero: true },
                },
            },
        });
    </script>
@endsection
