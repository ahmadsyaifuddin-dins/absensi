<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Riwayat Absensi</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 11px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
        }
        .header p {
            margin: 2px 0;
            font-size: 12px;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse; /* Menyatukan border */
            table-layout: fixed; /* Mencegah tabel auto-sizing yang bisa merusak layout */
        }
        .main-table th, .main-table td {
            border: 1px solid #a0a0a0;
            padding: 6px;
            word-wrap: break-word; /* Memaksa teks panjang untuk pindah baris */
        }
        .main-table thead th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .footer {
            margin-top: 20px;
            font-size: 9px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Riwayat Absensi</h1>
        <p><strong>Nama Karyawan:</strong> {{ $employee->nama_lengkap }}</p>
        <p><strong>NIP:</strong> {{ $employee->nip }}</p>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">Tanggal</th>
                <th style="width: 15%;">Jam Masuk</th>
                <th style="width: 15%;">Jam Pulang</th>
                <th style="width: 35%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($attendances as $attendance)
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->date)->isoFormat('dddd, D MMMM Y') }}</td>
                    <td style="text-align: center;">{{ $attendance->time_in ?? '--:--' }}</td>
                    <td style="text-align: center;">{{ $attendance->time_out ?? '--:--' }}</td>
                    <td>{{ $attendance->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada data riwayat absensi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now('Asia/Makassar')->isoFormat('D MMMM Y, HH:mm') }} WITA
    </div>
</body>
</html>