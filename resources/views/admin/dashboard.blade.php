@extends('partial.main')

@section('body')
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="text-center">Dashboard</h1>
            <p class="text-center text-muted">Pantau statistik status gizi balita dengan mudah.</p>
        </div>
    </div>

    <div class="row">
        <!-- Statistik Kartu -->
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5 class="card-title">Jumlah Pengguna</h5>
                    <h2 class="fw-bold">{{ $jumlahPengguna }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5 class="card-title">Jumlah Balita</h5>
                    <h2 class="fw-bold">{{ $jumlahBalita }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5 class="card-title">Sehat</h5>
                    <h2 class="fw-bold">{{ $jumlahBalitaLulus }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5 class="card-title">Teridentifikasi Stunting</h5>
                    <h2 class="fw-bold text-danger">{{ $balitaTeridentifikasiStunting }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Chart BB/U -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-center">Grafik BB/U</h5>
                    <canvas id="chartBBU"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart TB/U -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-center">Grafik TB/U</h5>
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
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                    },
                    {
                        label: 'Gizi Kurang',
                        data: @json($bb_u_gizi_kurang),
                        backgroundColor: 'rgba(255, 159, 64, 0.6)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1,
                    },
                    {
                        label: 'Gizi Buruk',
                        data: @json($bb_u_gizi_buruk),
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                    },
                    {
                        label: 'Obesitas',
                        data: @json($bb_u_obesitas),
                        backgroundColor: 'rgba(255, 206, 86, 0.6)',
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: 'Grafik BB/U per Tahun' },
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
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                    },
                    {
                        label: 'Sangat Pendek',
                        data: @json($tb_u_stunting),
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                    },
                    {
                        label: 'Pendek',
                        data: @json($tb_u_pendek),
                        backgroundColor: 'rgba(153, 102, 255, 0.6)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: 'Grafik TB/U per Tahun' },
                },
            },
        });
    </script>
@endsection
