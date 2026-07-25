<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 8mm 8mm 8mm;
        }
        body {
            font-family: sans-serif;
            font-size: 7.5pt;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
        }
        .header h1 {
            font-size: 13pt;
            margin: 0 0 2px 0;
            text-transform: uppercase;
            color: #0f172a;
        }
        .header p {
            font-size: 8pt;
            margin: 0;
            color: #64748b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            table-layout: fixed;
        }
        th, td {
            border: 0.5pt solid #cbd5e1;
            padding: 2.5pt 1.5pt;
            text-align: center;
            font-size: 6.5pt;
            word-wrap: break-word;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #334155;
        }
        .th-name {
            width: 95pt;
            text-align: left;
            padding-left: 4pt;
        }
        .th-nis {
            width: 40pt;
        }
        .th-class {
            width: 35pt;
        }
        .th-stat {
            width: 14pt;
        }
        .th-pct {
            width: 22pt;
        }
        .td-name {
            text-align: left;
            padding-left: 4pt;
            font-weight: bold;
            font-size: 7pt;
        }
        .bg-weekend {
            background-color: #f8fafc;
            color: #94a3b8;
        }
        .bg-hadir {
            background-color: #dcfce7;
            color: #15803d;
            font-weight: bold;
        }
        .bg-terlambat {
            background-color: #fef9c3;
            color: #a16207;
            font-weight: bold;
        }
        .bg-izin {
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: bold;
        }
        .bg-sakit {
            background-color: #e0e7ff;
            color: #4338ca;
            font-weight: bold;
        }
        .bg-alpa {
            background-color: #fee2e2;
            color: #b91c1c;
            font-weight: bold;
        }
        .legend {
            margin-top: 10px;
            font-size: 7pt;
            display: flex;
            gap: 15px;
        }
        .footer {
            margin-top: 12px;
            font-size: 7.5pt;
        }
        .signature-table {
            width: 100%;
            border: none;
            margin-top: 15px;
        }
        .signature-table td {
            border: none;
            text-align: center;
            font-size: 8pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Bulan: {{ \Carbon\Carbon::parse($yearMonth . '-01')->isoFormat('MMMM YYYY') }} | Dicetak: {{ $generated_at }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 15pt;">No</th>
                <th rowspan="2" class="th-name">Nama Murid</th>
                <th rowspan="2" class="th-nis">NIS</th>
                <th rowspan="2" class="th-class">Kelas</th>
                <th colspan="5">Rekapitulasi</th>
                <th rowspan="2" class="th-pct">%</th>
                <th colspan="{{ count($days) }}">Tanggal (Bulan Ini)</th>
            </tr>
            <tr>
                <th class="th-stat">H</th>
                <th class="th-stat">T</th>
                <th class="th-stat">I</th>
                <th class="th-stat">S</th>
                <th class="th-stat">A</th>
                @foreach($days as $dateStr => $day)
                    <th class="{{ $day['isWeekend'] || isset($holidays[$dateStr]) ? 'bg-weekend' : '' }}">
                        {{ $day['dayNumber'] }}<br><span style="font-size: 5pt; font-weight: normal;">{{ $day['dayName'] }}</span>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($matrix as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="td-name">{{ $row['student']->user->name }}</td>
                    <td>{{ $row['student']->nis }}</td>
                    <td>{{ $row['student']->classRoom->name ?? '-' }}</td>
                    <td>{{ $row['summary']['hadir'] }}</td>
                    <td>{{ $row['summary']['terlambat'] }}</td>
                    <td>{{ $row['summary']['izin'] }}</td>
                    <td>{{ $row['summary']['sakit'] }}</td>
                    <td>{{ $row['summary']['alpa'] }}</td>
                    <td><strong>{{ $row['summary']['percentage'] }}%</strong></td>

                    @foreach($days as $dateStr => $day)
                        @php
                            $rec = $row['dateRecords'][$dateStr] ?? null;
                            $lbl = $rec['label'] ?? '';
                            $bgClass = match(strtolower($rec['status'] ?? '')) {
                                'hadir' => 'bg-hadir',
                                'terlambat' => 'bg-terlambat',
                                'izin' => 'bg-izin',
                                'sakit' => 'bg-sakit',
                                'alpa' => 'bg-alpa',
                                default => ($day['isWeekend'] || isset($holidays[$dateStr])) ? 'bg-weekend' : ''
                            };
                        @endphp
                        <td class="{{ $bgClass }}">
                            @if($rec && $rec['time'])
                                <span style="font-size: 5.5pt; display: block;">{{ $rec['time'] }}</span>
                                <span style="font-size: 6.5pt;">{{ $lbl }}</span>
                            @else
                                {{ $lbl }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 10 + count($days) }}" style="text-align: center; padding: 15px; color: #94a3b8;">
                        Tidak ada data murid / presensi untuk filter ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 8px; font-size: 6.5pt; color: #475569;">
        <strong>Keterangan Status:</strong>
        <span style="color: #15803d; font-weight: bold;">H</span> = Hadir |
        <span style="color: #a16207; font-weight: bold;">T</span> = Terlambat |
        <span style="color: #0369a1; font-weight: bold;">I</span> = Izin |
        <span style="color: #4338ca; font-weight: bold;">S</span> = Sakit |
        <span style="color: #b91c1c; font-weight: bold;">A</span> = Alpa |
        <span>-</span> = Libur / Akhir Pekan
    </div>

    <table class="signature-table">
        <tr>
            <td style="width: 70%;"></td>
            <td>
                {{ config('app.name') }}, {{ now()->isoFormat('D MMMM YYYY') }}<br>
                Kepala Sekolah / Admin Presensi,<br><br><br><br>
                <strong>________________________</strong>
            </td>
        </tr>
    </table>
</body>
</html>
