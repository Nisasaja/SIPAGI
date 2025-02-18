@extends('partial.main')

@section('body')
    <div class="container mt-5">
        <h1 class="mb-4">Grafik KMS Balita: {{ $profile->nama_anak }}</h1>

        <!-- Form untuk memilih bulan -->
        <form method="GET" action="{{ route('pengukuran.kms', ['id' => $profile->id]) }}">
            <div class="form-group">
                <label for="month">Pilih Bulan</label>
                <select id="month" name="month" class="form-control">
                    <option value="">Semua Bulan</option>
                    <option value="1" {{ $month == 1 ? 'selected' : '' }}>Januari</option>
                    <option value="2" {{ $month == 2 ? 'selected' : '' }}>Februari</option>
                    <option value="3" {{ $month == 3 ? 'selected' : '' }}>Maret</option>
                    <option value="4" {{ $month == 4 ? 'selected' : '' }}>April</option>
                    <option value="5" {{ $month == 5 ? 'selected' : '' }}>Mei</option>
                    <option value="6" {{ $month == 6 ? 'selected' : '' }}>Juni</option>
                    <option value="7" {{ $month == 7 ? 'selected' : '' }}>Juli</option>
                    <option value="8" {{ $month == 8 ? 'selected' : '' }}>Agustus</option>
                    <option value="9" {{ $month == 9 ? 'selected' : '' }}>September</option>
                    <option value="10" {{ $month == 10 ? 'selected' : '' }}>Oktober</option>
                    <option value="11" {{ $month == 11 ? 'selected' : '' }}>November</option>
                    <option value="12" {{ $month == 12 ? 'selected' : '' }}>Desember</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Tampilkan Grafik</button>
        </form>

        <button id="downloadBBU" class="btn btn-primary mt-3">Download BB Chart</button>
        <button id="downloadTBU" class="btn btn-primary mt-3">Download TB Chart</button>

        <div class="row">
            <div class="col-md-6">
                <!-- Grafik BB/U -->
                <canvas id="bbUChart"></canvas>
            </div>
            <div class="col-md-6">
                <!-- Grafik TB/U -->
                <canvas id="tbUChart"></canvas>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var bbUData = @json($bbData);
        var tbUData = @json($tbData);
        var dates = @json($dates);

        var ctxBBU = document.getElementById('bbUChart').getContext('2d');
        var bbUChart = new Chart(ctxBBU, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'BB',
                    data: bbUData,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });

        var ctxTBU = document.getElementById('tbUChart').getContext('2d');
        var tbUChart = new Chart(ctxTBU, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'TB',
                    data: tbUData,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });

        document.getElementById('downloadBBU').addEventListener('click', function() {
            var url = bbUChart.toBase64Image();
            var a = document.createElement('a');
            a.href = url;
            a.download = 'BB-U_Chart.png';
            a.click();
        });

        document.getElementById('downloadTBU').addEventListener('click', function() {
            var url = tbUChart.toBase64Image();
            var a = document.createElement('a');
            a.href = url;
            a.download = 'TB-U_Chart.png';
            a.click();
        });
    </script>
    <style>
        #bbUChart, #tbUChart {
            max-height: 400px;
            margin-top: 30px;
        }
    </style>
@endsection
