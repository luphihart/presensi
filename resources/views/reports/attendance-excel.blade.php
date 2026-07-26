<table>
    <thead>
        <tr>
            <th colspan="{{ 10 + count($days) }}" style="font-weight: bold; font-size: 14pt; text-align: center;">
                {{ $title }}
            </th>
        </tr>
        <tr>
            <th colspan="{{ 10 + count($days) }}" style="text-align: center; color: #666666;">
                Bulan: {{ \Carbon\Carbon::parse($yearMonth . '-01')->locale('id')->isoFormat('MMMM YYYY') }} | Dicetak: {{ $generated_at }} WIB
            </th>
        </tr>
        <tr>
            <th rowspan="2" style="font-weight: bold; text-align: center; background-color: #e2e8f0; border: 1px solid #cbd5e1;">No</th>
            <th rowspan="2" style="font-weight: bold; text-align: left; background-color: #e2e8f0; border: 1px solid #cbd5e1;">Nama Murid</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; background-color: #e2e8f0; border: 1px solid #cbd5e1;">NIS</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; background-color: #e2e8f0; border: 1px solid #cbd5e1;">Kelas</th>
            <th colspan="5" style="font-weight: bold; text-align: center; background-color: #cbd5e1; border: 1px solid #cbd5e1;">Rekapitulasi</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; background-color: #e2e8f0; border: 1px solid #cbd5e1;">%</th>
            <th colspan="{{ count($days) }}" style="font-weight: bold; text-align: center; background-color: #e2e8f0; border: 1px solid #cbd5e1;">Rincian Tanggal</th>
        </tr>
        <tr>
            <th style="font-weight: bold; text-align: center; background-color: #dcfce7; border: 1px solid #cbd5e1;">H</th>
            <th style="font-weight: bold; text-align: center; background-color: #fef9c3; border: 1px solid #cbd5e1;">T</th>
            <th style="font-weight: bold; text-align: center; background-color: #e0f2fe; border: 1px solid #cbd5e1;">I</th>
            <th style="font-weight: bold; text-align: center; background-color: #e0e7ff; border: 1px solid #cbd5e1;">S</th>
            <th style="font-weight: bold; text-align: center; background-color: #fee2e2; border: 1px solid #cbd5e1;">A</th>

            @foreach($days as $dateStr => $day)
                <th style="font-weight: bold; text-align: center; background-color: {{ $day['isWeekend'] || isset($holidays[$dateStr]) ? '#f1f5f9' : '#e2e8f0' }}; border: 1px solid #cbd5e1;">
                    {{ $day['dayNumber'] }} {{ $day['dayName'] }}
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($matrix as $index => $row)
            <tr>
                <td style="text-align: center; border: 1px solid #cbd5e1;">{{ $index + 1 }}</td>
                <td style="text-align: left; font-weight: bold; border: 1px solid #cbd5e1;">{{ $row['student']->user->name }}</td>
                <td style="text-align: center; border: 1px solid #cbd5e1;">{{ $row['student']->nis }}</td>
                <td style="text-align: center; border: 1px solid #cbd5e1;">{{ $row['student']->classRoom->name ?? '-' }}</td>
                <td style="text-align: center; font-weight: bold; color: #15803d; border: 1px solid #cbd5e1;">{{ $row['summary']['hadir'] }}</td>
                <td style="text-align: center; font-weight: bold; color: #a16207; border: 1px solid #cbd5e1;">{{ $row['summary']['terlambat'] }}</td>
                <td style="text-align: center; font-weight: bold; color: #0369a1; border: 1px solid #cbd5e1;">{{ $row['summary']['izin'] }}</td>
                <td style="text-align: center; font-weight: bold; color: #4338ca; border: 1px solid #cbd5e1;">{{ $row['summary']['sakit'] }}</td>
                <td style="text-align: center; font-weight: bold; color: #b91c1c; border: 1px solid #cbd5e1;">{{ $row['summary']['alpa'] }}</td>
                <td style="text-align: center; font-weight: bold; border: 1px solid #cbd5e1;">{{ $row['summary']['percentage'] }}%</td>

                @foreach($days as $dateStr => $day)
                    @php
                        $rec = $row['dateRecords'][$dateStr] ?? null;
                        $lbl = $rec['label'] ?? '';
                        $cellBg = match(strtolower($rec['status'] ?? '')) {
                            'hadir' => '#dcfce7',
                            'terlambat' => '#fef9c3',
                            'izin' => '#e0f2fe',
                            'sakit' => '#e0e7ff',
                            'alpa' => '#fee2e2',
                            default => ($day['isWeekend'] || isset($holidays[$dateStr])) ? '#f8fafc' : '#ffffff'
                        };
                    @endphp
                    <td style="text-align: center; background-color: {{ $cellBg }}; border: 1px solid #cbd5e1;">
                        @if($rec && $rec['time'])
                            {{ $rec['time'] }} ({{ $lbl }})
                        @else
                            {{ $lbl }}
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
