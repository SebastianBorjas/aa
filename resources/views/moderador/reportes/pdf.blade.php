<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; }
        .chart { display: flex; align-items: flex-end; height: 150px; margin-top: 20px; }
        .bar { width: 60px; margin-right: 10px; background: #eee; position: relative; }
        .fill { position: absolute; bottom: 0; left: 0; width: 100%; }
        .label { text-align: center; margin-top: 5px; font-size: 12px; }
    </style>
</head>
<body>
    <h1>Reporte de {{ \Illuminate\Support\Str::title($alumno->name) }}</h1>
    <p>Periodo: {{ $fi->toDateString() }} - {{ $ff->toDateString() }}</p>

    <h2>Asistencia</h2>
    @php $total = max(1, array_sum($attendanceSummary)); @endphp
    <div class="chart">
        <div class="bar">
            <div class="fill" style="height:{{ $attendanceSummary['asistencia']/$total*100 }}%; background:#16a34a;"></div>
        </div>
        <div class="bar">
            <div class="fill" style="height:{{ $attendanceSummary['falta']/$total*100 }}%; background:#dc2626;"></div>
        </div>
        <div class="bar">
            <div class="fill" style="height:{{ $attendanceSummary['justificado']/$total*100 }}%; background:#fbbf24;"></div>
        </div>
    </div>
    <div style="display:flex;">
        <div class="label" style="width:60px">Asist.</div>
        <div class="label" style="width:70px">Faltas</div>
        <div class="label" style="width:80px">Justif.</div>
    </div>
    <p>Total días registrados: {{ $total }}</p>

    <h2>Actividades entregadas: {{ $entregas->count() }}</h2>
    <ul>
        @foreach($entregas as $entrega)
            <li>{{ $entrega->subtema->name ?? 'Actividad' }} - {{ $entrega->created_at->format('Y-m-d') }}</li>
        @endforeach
    </ul>
</body>
</html>
