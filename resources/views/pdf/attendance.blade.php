<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekod Kehadiran</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; margin: 0; padding: 20px; }
        h2 { text-align: center; margin-bottom: 4px; }
        .subtitle { text-align: center; color: #555; margin-bottom: 16px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th {
            background-color: #1e5c7b;
            color: #fff;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            padding: 10px 12px;
            text-align: left;
        }
        td { padding: 8px 12px; font-size: 11px; text-transform: uppercase; }
        tr:nth-child(even) { background-color: #ddeef6; }
        tr:nth-child(odd) { background-color: #fff; }
        .no-col { width: 4%; text-align: center; }
        .nama-col { width: 18%; }
        .ic-col { width: 14%; }
        .jawatan-col { width: 12%; }
        .tel-col { width: 12%; }
        .alamat-col { width: 20%; }
        .status-col { width: 10%; }
        .sebab-col { width: 10%; }
        .status-hadir { color: #166534; font-weight: bold; }
        .status-tidak-hadir { color: #991b1b; font-weight: bold; }
        .status-lewat { color: #92400e; font-weight: bold; }
        .status-dimaafkan { color: #1e40af; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Rekod Kehadiran — {{ $meetingTitle }}</h2>
    <p class="subtitle">Kategori: {{ $categoryLabel }} | Tarikh: {{ $meetingDate }}</p>

    <table>
        <thead>
            <tr>
                <th class="no-col">No.</th>
                <th class="nama-col">Nama</th>
                <th class="ic-col">No. Kad Pengenalan</th>
                <th class="jawatan-col">Jawatan</th>
                <th class="tel-col">No Telefon</th>
                <th class="alamat-col">Alamat</th>
                <th class="status-col">Status</th>
                <th class="sebab-col">Sebab</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $i => $row)
                @php
                    $statusClass = match($row['status'] ?? '') {
                        'Hadir' => 'status-hadir',
                        'Tidak Hadir' => 'status-tidak-hadir',
                        'Lewat' => 'status-lewat',
                        'Dimaafkan' => 'status-dimaafkan',
                        default => '',
                    };
                @endphp
                <tr>
                    <td class="no-col">{{ $i + 1 }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['ic_number'] }}</td>
                    <td>{{ $row['position_type'] ?? '-' }}</td>
                    <td>{{ $row['phone_number'] ?? '-' }}</td>
                    <td>{{ $row['address'] ?? '-' }}</td>
                    <td class="{{ $statusClass }}">{{ $row['status'] ?? '-' }}</td>
                    <td>{{ $row['absence_reason'] ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;">Tiada rekod.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
