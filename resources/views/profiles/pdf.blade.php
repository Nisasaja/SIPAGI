<!DOCTYPE html>
<html>
<head>
    <title>Data Profile</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Data Profile</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Anak</th>
                <th>Jenis Kelamin</th>
                <th>Tanggal Lahir</th>
                <th>Anak Ke</th>
                <th>Status ASI</th>
                <th>Status Imunisasi</th>
                <th>BB Lahir (kg)</th>
                <th>TB Lahir (cm)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($profiles as $profile)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $profile->nama_anak }}</td>
                    <td>{{ $profile->jenis_kelamin }}</td>
                    <td>{{ \Carbon\Carbon::parse($profile->tanggal_lahir)->format('d M Y') }}</td>
                    <td>{{ $profile->anak_ke }}</td>
                    <td>{{ $profile->status_asi }}</td>
                    <td>{{ $profile->status_imunisasi }}</td>
                    <td>{{ number_format($profile->bb_lahir, 2) }}</td>
                    <td>{{ number_format($profile->tb_lahir, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
