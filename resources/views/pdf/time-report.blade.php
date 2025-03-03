<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport de temps</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
        }
        .user-info {
            margin-bottom: 20px;
        }
        .week-header {
            background-color: #f5f5f5;
            padding: 8px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 14px;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #f9f9f9;
            text-align: left;
            padding: 8px;
            font-weight: bold;
        }
        td {
            padding: 8px;
            vertical-align: top;
        }
        .week-total {
            text-align: right;
            font-weight: bold;
            padding: 8px;
            margin-bottom: 20px;
        }
        .chart-container {
            width: 100%;
            margin: 20px 0;
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #999;
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Rapport de temps</div>
        <div class="subtitle">
            Période : {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }} | 
            Total : {{ $formatDuration($totalMinutes) }}
        </div>
    </div>
    
    <div class="user-info">
        <strong>Utilisateur :</strong> {{ $user->name }}<br>
        <strong>Email :</strong> {{ $user->email }}<br>
        <strong>Rapport généré le :</strong> {{ now()->format('d/m/Y H:i') }}
    </div>
    
    <!-- Résumé des heures travaillées -->
    <div class="chart-container">
        <h3 style="font-size: 14px; margin-bottom: 10px;">Répartition des heures travaillées</h3>
        
        @php
            // Regrouper les données par semaine pour éviter le débordement
            $weeklyData = [];
            $i = 0;
            $weekCount = 0;
            foreach($chartData['labels'] as $index => $label) {
                if ($i % 7 == 0) {
                    $weekCount++;
                    $weeklyData[$weekCount] = ['labels' => [], 'data' => []];
                }
                $weeklyData[$weekCount]['labels'][] = $label;
                $weeklyData[$weekCount]['data'][] = $chartData['data'][$index];
                $i++;
            }
        @endphp
        
        @foreach($weeklyData as $week)
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                <thead>
                    <tr>
                        @foreach($week['labels'] as $label)
                            <th style="border: 1px solid #ddd; padding: 5px; text-align: center; font-size: 10px;">{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach($week['data'] as $hours)
                            <td style="border: 1px solid #ddd; padding: 5px; text-align: center; font-size: 10px;">
                                @php
                                    $h = floor($hours);
                                    $m = round(($hours - $h) * 60);
                                    echo $h . 'h' . ($m > 0 ? ' ' . $m . 'min' : '');
                                @endphp
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        @endforeach
    </div>
    
    @if(count($entriesByWeek) > 0)
        @foreach($entriesByWeek as $weekKey => $week)
            <div class="week-header">
                {{ $week['week_label'] }}
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="15%">Date</th>
                        <th width="25%">Ticket</th>
                        <th width="10%">Durée</th>
                        <th width="40%">Description</th>
                        <th width="10%">Facturable</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($week['entries'] as $entry)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($entry->entry_date)->format('d/m/Y') }}</td>
                            <td>{{ $entry->ticket->title ?? 'N/A' }}</td>
                            <td>{{ $formatDuration($entry->minutes_spent) }}</td>
                            <td>{{ $entry->description ?? '-' }}</td>
                            <td>{{ $entry->billable ? 'Oui' : 'Non' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="week-total">
                Total de la semaine : {{ $formatDuration($week['total_minutes']) }}
            </div>
        @endforeach
    @else
        <p>Aucune entrée de temps pour la période sélectionnée.</p>
    @endif
    
    <div class="footer">
        Rapport généré automatiquement le {{ now()->format('d/m/Y à H:i') }}
    </div>
</body>
</html>
