<html>
    <head>
        <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #222; }
        h2 { margin: 0 0 6px 0; font-size: 16px; }
        .meta { margin-bottom: 12px; }
        .meta div { margin: 2px 0; }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th { background: #f5f5f5; }
        </style>
    </head>
    <body>
        <h2>Prescription</h2>
        <div class="meta">
            <div><strong>Doctor:</strong> {{ $doctor_name ?? 'Doctor' }}</div>
            <div><strong>Patient:</strong> {{ $patient_name ?? 'Patient' }}</div>
            <div><strong>Valid From:</strong> {{ $valid_from ?? '-' }}</div>
            <div><strong>Valid Until:</strong> {{ $valid_until ?? '-' }}</div>
        </div>

        @php
            $items = $medicines ?? [];
        @endphp

        <table>
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Strength</th>
                    <th>Days</th>
                    <th>Frequency</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    @php
                        $medicine = data_get($item, 'medicine', '-');
                        $strength = data_get($item, 'strength', '-');
                        $days = data_get($item, 'days', '-');
                        $morning = data_get($item, 'morning', null);
                        $afternoon = data_get($item, 'afternoon', null);
                        $night = data_get($item, 'night', null);
                        $hasFrequency = $morning !== null || $afternoon !== null || $night !== null;
                        $frequency = $hasFrequency
                            ? (($morning ? '1' : '0') . ' / ' . ($afternoon ? '1' : '0') . ' / ' . ($night ? '1' : '0'))
                            : '-';
                    @endphp
                    <tr>
                        <td>{{ $medicine }}</td>
                        <td>{{ $strength }}</td>
                        <td>{{ $days }}</td>
                        <td>{{ $frequency }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No medicines found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </body>
</html>
