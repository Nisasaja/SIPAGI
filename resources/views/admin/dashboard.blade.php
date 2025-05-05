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
        @foreach ([
            ['title' => 'Jumlah Pengguna', 'value' => $jumlahPengguna],
            ['title' => 'Jumlah Balita', 'value' => $jumlahBalita],
            ['title' => 'TB Balita Normal', 'value' => $jumlahBalitaLulus],
            ['title' => 'Teridentifikasi Stunting', 'value' => $balitaTeridentifikasiStunting, 'class' => 'text-danger']
        ] as $stat)
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5 class="card-title">{{ $stat['title'] }}</h5>
                    <h2 class="fw-bold {{ $stat['class'] ?? '' }}">{{ $stat['value'] }}</h2>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row mt-4">
        <!-- Chart BB/U -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="card-title text-center">Grafik BB/U</h5>
                    <select id="yearSelectBBU" class="form-select w-auto mb-3">
                        @foreach ($tahun as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                    <canvas id="chartBBU"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart TB/U -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="card-title text-center">Grafik TB/U</h5>
                    <select id="yearSelectTBU" class="form-select w-auto mb-3">
                        @foreach ($tahun as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                    <canvas id="chartTBU"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Grafik Status BB/U Per Bulan -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Status BB/U Bulanan</h5>
                    <select id="yearSelectBBUBulanan" class="form-select w-auto">
                        @foreach ($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:400px;">
                        <canvas id="grafikBBUBulanan"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Grafik Status TB/U Per Bulan -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Status TB/U Bulanan</h5>
                    <select id="yearSelectTBUBulanan" class="form-select w-auto">
                        @foreach ($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:400px;">
                        <canvas id="grafikTBUBulanan"></canvas>
                    </div>
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
        let chartBBU = new Chart(ctxBBU, {
            type: 'bar',
            data: {
                labels: ['Normal', 'Gizi Buruk', 'Gizi Kurang', 'Gizi Lebih', 'Obesitas'],
                datasets: [{
                    label: 'Jumlah',
                    data: @json(array_values($bb_u_data[$tahun[0]])),
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(255, 159, 64, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(153, 102, 255, 0.6)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
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
        let chartTBU = new Chart(ctxTBU, {
            type: 'bar',
            data: {
                labels: ['Normal', 'Sangat Pendek', 'Pendek', 'Tinggi Badan Lebih'],
                datasets: [{
                    label: 'Jumlah',
                    data: @json(array_values($tb_u_data[$tahun[0]])),
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(255, 159, 64, 0.6)',
                        'rgba(153, 102, 255, 0.6)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: 'Grafik TB/U per Tahun' },
                },
            },
        });

        // Function to update charts based on selected year
        function updateCharts(year, chartBBU, chartTBU) {
            if (chartBBU) {
                chartBBU.data.datasets[0].data = @json($bb_u_data)[year];
                chartBBU.update();
            }

            if (chartTBU) {
                chartTBU.data.datasets[0].data = @json($tb_u_data)[year];
                chartTBU.update();
            }
        }

        // Inisialisasi Grafik BB/U Bulanan
        var ctxBBUBulanan = document.getElementById('grafikBBUBulanan').getContext('2d');
        var grafikBBUBulanan = new Chart(ctxBBUBulanan, {
            type: 'line',
            data: {
                labels: @json($bulan),
                datasets: [
                    {
                        label: 'Normal',
                        data: @json($bb_u_normal_bulanan),
                        borderColor: 'rgba(40, 167, 69, 1)',
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        fill: true,
                        pointRadius: 4,
                    },
                    {
                        label: 'Gizi Buruk',
                        data: @json($bb_u_gizi_buruk_bulanan),
                        borderColor: 'rgba(210, 4, 45, 1)',
                        backgroundColor: 'rgba(210, 4, 45, 0.2)',
                        fill: true,
                        pointRadius: 4,
                    },
                    {
                        label: 'Gizi Kurang',
                        data: @json($bb_u_gizi_kurang_bulanan),
                        borderColor: 'rgba(255, 193, 7, 1)',
                        backgroundColor: 'rgba(255, 193, 7, 0.2)',
                        fill: true,
                        pointRadius: 4,
                    },
                    {
                        label: 'Gizi Lebih',
                        data: @json($bb_u_gizi_lebih_bulanan),
                        borderColor: 'rgba(23, 162, 184, 1)',
                        backgroundColor: 'rgba(23, 162, 184, 0.2)',
                        fill: true,
                        pointRadius: 4,
                    },
                    {
                        label: 'Obesitas',
                        data: @json($bb_u_obesitas_bulanan),
                        borderColor: 'rgba(108, 117, 125, 1)',
                        backgroundColor: 'rgba(108, 117, 125, 0.2)',
                        fill: true,
                        pointRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Balita'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Grafik Status BB/U Bulanan',
                        font: {
                            size: 16
                        }
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw + ' balita';
                            }
                        }
                    }
                }
            }
        });

        // Inisialisasi Grafik TB/U Bulanan (menggunakan format yang sama)
        var ctxTBUBulanan = document.getElementById('grafikTBUBulanan').getContext('2d');
        var grafikTBUBulanan = new Chart(ctxTBUBulanan, {
            type: 'line',
            data: {
                labels: @json($bulan),
                datasets: [
                    {
                        label: 'Normal',
                        data: @json($tb_u_normal_bulanan),
                        borderColor: 'rgba(40, 167, 69, 1)',
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        fill: true,
                        pointRadius: 4,
                    },
                    {
                        label: 'Sangat Pendek',
                        data: @json($tb_u_stunting_bulanan),
                        borderColor: 'rgba(220, 53, 69, 1)',
                        backgroundColor: 'rgba(220, 53, 69, 0.2)',
                        fill: true,
                        pointRadius: 4,
                    },
                    {
                        label: 'Pendek',
                        data: @json($tb_u_pendek_bulanan),
                        borderColor: 'rgba(255, 193, 7, 1)',
                        backgroundColor: 'rgba(255, 193, 7, 0.2)',
                        fill: true,
                        pointRadius: 4,
                    },
                    {
                        label: 'Tinggi Badan Lebih',
                        data: @json($tb_u_tinggi_lebih_bulanan),
                        borderColor: 'rgba(23, 162, 184, 1)',
                        backgroundColor: 'rgba(23, 162, 184, 0.2)',
                        fill: true,
                        pointRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Balita'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Grafik Status TB/U Bulanan',
                        font: {
                            size: 16
                        }
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw + ' balita';
                            }
                        }
                    }
                }
            }
        });

        // Event listener for year selection
        document.getElementById('yearSelectBBU').addEventListener('change', function() {
            updateCharts(this.value, chartBBU, null);
        });
        
        document.getElementById('yearSelectTBU').addEventListener('change', function() {
            updateCharts(this.value, null, chartTBU);
        });

        document.getElementById('yearSelectBBUBulanan').addEventListener('change', function() {
            updateCharts(this.value, grafikBBUBulanan, null);
        });

        document.getElementById('yearSelectTBUBulanan').addEventListener('change', function() {
            updateCharts(this.value, null, grafikTBUBulanan);
        });
    </script>
@endsection

