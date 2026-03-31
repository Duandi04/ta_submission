<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengajuan Proposal Tugas Akhir</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 2cm;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            border-bottom: 2px solid #000;
            padding-bottom: 1rem;
        }

        .header h1 {
            font-size: 16pt;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }

        .header p {
            margin: 0;
            font-size: 12pt;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }

        .table th, .table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
        }

        .table th {
            font-weight: bold;
            text-align: center;
            background-color: #f8f9fa; /* Only shows if bg-colors are printed */
        }

        .text-center { text-align: center; }
        
        .footer {
            margin-top: 3rem;
            text-align: right;
        }

        @media print {
            body { padding: 0; margin: 0; }
            @page { margin: 2cm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; font-size: 14px; cursor: pointer; background: #0d6efd; color: white; border: none; border-radius: 4px;">Print Laporan</button>
    </div>

    <div class="header">
        <h1>Laporan Pengajuan Proposal Tugas Akhir Diterima</h1>
        <p>Program Studi: {{ auth()->user()->programStudi->name ?? '-' }}</p>
        <p>Tanggal Cetak: {{ now()->format('d/m/Y') }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Mahasiswa</th>
                <th style="width: 15%;">NIM</th>
                <th style="width: 35%;">Judul Proposal Diterima</th>
                <th style="width: 20%;">Dosen Pembimbing</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $student)
                @php
                    $accepted = $student->thesisSubmissions->first();
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $student->name }}</td>
                    <td class="text-center">{{ $student->nim_nip }}</td>
                    <td>{{ $accepted ? $accepted->title : '-' }}</td>
                    <td>{{ $accepted && $accepted->supervisor ? $accepted->supervisor->name : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 2rem;">Tidak ada data mahasiswa dengan proposal diterima.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p style="margin-bottom: 60px;">Mengetahui,<br>Ketua Program Studi</p>
        <p><strong>{{ auth()->user()->name }}</strong></p>
        <p style="margin-top: -10px;">{{ auth()->user()->nim_nip ?? 'NIP: ......................' }}</p>
    </div>

    <script>
        // Auto print when loaded
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
