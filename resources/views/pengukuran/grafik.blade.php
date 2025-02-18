@extends('partial.main')

@section('styles')
    <!-- Link FontAwesome dengan atribut integrity yang benar -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-yZlZs0x2Uu30tXK0WKyhkvX7dYX8oTSr9FP5CeN1E5WzXw9e3p7MKc1VnV+eXq+dB2q2RF6C1QiXowv3w8zHwQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('asset/css/pengukuran/grafik.css') }}">
@endsection

@section('body')
    <div class="container">
        <h1 class="mt-5 text-center">{{ $judul }}</h1>
        
        <!-- Dropdown untuk memilih bulan dan tahun -->
        <div class="mb-4 d-flex justify-content-center">
            <div class="me-3">
                <label for="bulanSelect" class="form-label">Pilih Bulan:</label>
                <select id="bulanSelect" class="form-select">
                    @foreach ($bulanList as $bulan)
                        <option value="{{ $bulan }}">{{ $bulan }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="tahunSelect" class="form-label">Pilih Tahun:</label>
                <select id="tahunSelect" class="form-select">
                    @foreach ($tahunList as $tahun)
                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <h4>Grafik BB/U (Berat Badan per Umur)</h4>
                <canvas id="bbuChart" style="width: 100%; height: 400px;"></canvas>
            </div>
            <div class="col-md-6">
                <h4>Grafik TB/U (Tinggi Badan per Umur)</h4>
                <canvas id="tbuChart" style="width: 100%; height: 400px;"></canvas>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Sertakan Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        let bbuChart, tbuChart;

        document.addEventListener('DOMContentLoaded', function () {
            // Ambil bulan dan tahun awal sebagai default
            const initialBulan = @json($bulanList)[0]; // Bulan pertama
            const initialTahun = @json($tahunList)[0]; // Tahun pertama

            document.getElementById('bulanSelect').value = initialBulan;
            document.getElementById('tahunSelect').value = initialTahun;

            initializeCharts(initialBulan, initialTahun);
        });

        function initializeCharts(selectedBulan, selectedTahun) {
            // Fetch data untuk bulan dan tahun yang dipilih
            fetch(`/data-per-bulan-tahun/${encodeURIComponent(selectedBulan)}/${encodeURIComponent(selectedTahun)}?t=${new Date().getTime()}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    // Hitung nilai maksimum untuk BB/U dan TB/U
                    const maxBBU = Math.max(data.bb_u_normal, data.bb_u_sangat_kurang, data.bb_u_gizi_kurang, data.bb_u_gizi_lebih, data.bb_u_obesitas, data.bb_u_lulus);
                    const maxTBU = Math.max(data.tb_u_normal, data.tb_u_sangat_pendek, data.tb_u_pendek, data.tb_u_tinggi, data.tb_u_lulus);
                    
                    // Tambahkan buffer 10% ke nilai maksimum
                    const suggestedMaxBBU = Math.ceil(maxBBU * 1.1);
                    const suggestedMaxTBU = Math.ceil(maxTBU * 1.1);

                    // Inisialisasi Chart BB/U
                    const bbuCtx = document.getElementById('bbuChart').getContext('2d');
                    bbuChart = new Chart(bbuCtx, {
                        type: 'bar',
                        data: {
                            labels: [selectedBulan + ' ' + selectedTahun],
                            datasets: [
                                {
                                    label: 'Normal',
                                    data: [data.bb_u_normal],
                                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                },
                                {
                                    label: 'Sangat Kurang',
                                    data: [data.bb_u_sangat_kurang],
                                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                                },
                                {
                                    label: 'Gizi Kurang',
                                    data: [data.bb_u_gizi_kurang],
                                    backgroundColor: 'rgba(255, 159, 64, 0.6)',
                                },
                                {
                                    label: 'Gizi Lebih',
                                    data: [data.bb_u_gizi_lebih],
                                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                },
                                {{--  {
                                    label: 'Obesitas',
                                    data: [data.bb_u_obesitas],
                                    backgroundColor: 'rgba(153, 102, 255, 0.6)',
                                },  --}}
                                {
                                    label: 'Lulus',
                                    data: [data.bb_u_lulus],
                                    backgroundColor: 'rgba(153, 106, 250, 0.6)',
                                }
                            ]
                        },
                        options: {
                            plugins: {
                                title: {
                                    display: true,
                                    text: `Grafik BB/U Balita untuk ${selectedBulan} ${selectedTahun}`
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                },
                                legend: {
                                    position: 'top',
                                }
                            },
                            responsive: true,
                            scales: {
                                x: {
                                    stacked: false,
                                    title: {
                                        display: true,
                                        text: 'Posyandu'
                                    }
                                },
                                y: {
                                    stacked: false,
                                    beginAtZero: true,
                                    suggestedMax: suggestedMaxBBU, // Set suggestedMax dengan buffer
                                    title: {
                                        display: true,
                                        text: 'Jumlah'
                                    },
                                    ticks: {
                                        stepSize: 1, // Langkah bilangan bulat
                                        callback: function(value) {
                                            if (Number.isInteger(value)) {
                                                return value;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Inisialisasi Chart TB/U
                    const tbuCtx = document.getElementById('tbuChart').getContext('2d');
                    tbuChart = new Chart(tbuCtx, {
                        type: 'bar',
                        data: {
                            labels: [selectedBulan + ' ' + selectedTahun],
                            datasets: [
                                {
                                    label: 'Normal',
                                    data: [data.tb_u_normal],
                                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                },
                                {
                                    label: 'Sangat Pendek',
                                    data: [data.tb_u_sangat_pendek],
                                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                                },
                                {
                                    label: 'Pendek',
                                    data: [data.tb_u_pendek],
                                    backgroundColor: 'rgba(255, 206, 86, 0.6)',
                                },
                                {
                                    label: 'Tinggi',
                                    data: [data.tb_u_tinggi],
                                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                },
                                {
                                    label: 'Lulus',
                                    data: [data.tb_u_lulus],
                                    backgroundColor: 'rgba(153, 106, 250, 0.6)',
                                }
                            ]
                        },
                        options: {
                            plugins: {
                                title: {
                                    display: true,
                                    text: `Grafik TB/U Balita untuk ${selectedBulan} ${selectedTahun}`
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                },
                                legend: {
                                    position: 'top',
                                }
                            },
                            responsive: true,
                            scales: {
                                x: {
                                    stacked: false,
                                    title: {
                                        display: true,
                                        text: 'Posyandu'}
                                },
                                y: {
                                    stacked: false,
                                    beginAtZero: true,
                                    suggestedMax: suggestedMaxTBU, // Set suggestedMax dengan buffer
                                    title: {
                                        display: true,
                                        text: 'Jumlah'
                                    },
                                    ticks: {
                                        stepSize: 1, // Langkah bilangan bulat
                                        callback: function(value) {
                                            if (Number.isInteger(value)) {
                                                return value;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        function updateChart(selectedBulan, selectedTahun) {
            // Fetch data untuk bulan dan tahun yang dipilih
            fetch(`/data-per-bulan-tahun/${encodeURIComponent(selectedBulan)}/${encodeURIComponent(selectedTahun)}?t=${new Date().getTime()}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    // Hitung nilai maksimum untuk BB/U dan TB/U
                    const maxBBU = Math.max(data.bb_u_normal, data.bb_u_sangat_kurang, data.bb_u_gizi_kurang, data.bb_u_gizi_lebih, data.bb_u_obesitas, data.bb_u_lulus);
                    const maxTBU = Math.max(data.tb_u_normal, data.tb_u_sangat_pendek, data.tb_u_pendek, data.tb_u_tinggi, data.tb_u_lulus);
                    
                    // Tambahkan buffer 10% ke nilai maksimum
                    const suggestedMaxBBU = Math.ceil(maxBBU * 1.1);
                    const suggestedMaxTBU = Math.ceil(maxTBU * 1.1);

                    // Update Chart BB/U
                    bbuChart.data.labels = [selectedBulan + ' ' + selectedTahun];
                    bbuChart.data.datasets.forEach((dataset) => {
                        switch(dataset.label) {
                            case 'Normal':
                                dataset.data = [data.bb_u_normal];
                                break;
                            case 'Sangat Kurang':
                                dataset.data = [data.bb_u_sangat_kurang];
                                break;
                            case 'Gizi Kurang':
                                dataset.data = [data.bb_u_gizi_kurang];
                                break;
                            case 'Gizi Lebih':
                                dataset.data = [data.bb_u_gizi_lebih];
                                break;
                            case 'Obesitas':
                                dataset.data = [data.bb_u_obesitas];
                                break;
                            case 'Lulus':
                                dataset.data = [data.bb_u_lulus];
                                break;
                        }
                    });
                    bbuChart.options.plugins.title.text = `Grafik BB/U Balita untuk ${selectedBulan} ${selectedTahun}`;
                    bbuChart.options.scales.y.suggestedMax = suggestedMaxBBU; // Update suggestedMax
                    bbuChart.update();

                    // Update Chart TB/U
                    tbuChart.data.labels = [selectedBulan + ' ' + selectedTahun];
                    tbuChart.data.datasets.forEach((dataset) => {
                        switch(dataset.label) {
                            case 'Normal':
                                dataset.data = [data.tb_u_normal];
                                break;
                            case 'Sangat Pendek':
                                dataset.data = [data.tb_u_sangat_pendek];
                                break;
                            case 'Pendek':
                                dataset.data = [data.tb_u_pendek];
                                break;
                            case 'Tinggi':
                                dataset.data = [data.tb_u_tinggi];
                                break;
                            case 'Lulus':
                                dataset.data = [data.tb_u_lulus];
                                break;
                        }
                    });
                    tbuChart.options.plugins.title.text = `Grafik TB/U Balita untuk ${selectedBulan} ${selectedTahun}`;
                    tbuChart.options.scales.y.suggestedMax = suggestedMaxTBU; // Update suggestedMax
                    tbuChart.update();
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        // Menambahkan event listener pada dropdown bulan dan tahun
        document.getElementById('bulanSelect').addEventListener('change', function() {
            const selectedBulan = this.value;
            const selectedTahun = document.getElementById('tahunSelect').value;
            updateChart(selectedBulan, selectedTahun);
        });

        document.getElementById('tahunSelect').addEventListener('change', function() {
            const selectedTahun = this.value;
            const selectedBulan = document.getElementById('bulanSelect').value;
            updateChart(selectedBulan, selectedTahun);
        });
    </script>
@endsection


