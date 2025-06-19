<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { position: relative; margin-bottom: 20px; padding-right: 60px; }
        .logo { position: absolute; right: 0; top: 0; height: 40px; }
        h1 { font-size: 22px; margin: 0; }
        h2 { font-size: 18px; margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; font-size: 12px; }
        th { background: #f3f4f6; }
        .chart { text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <img class="logo" src="{{ $logo }}" alt="Logo">
        <div>
            <h1>Reporte de {{ \Illuminate\Support\Str::title($alumno->name) }}</h1>
            <p>Periodo: {{ $fi->toDateString() }} - {{ $ff->toDateString() }}</p>
        </div>
    </div>

    <p><strong>Especialidad:</strong> {{ $alumno->especialidad->nombre ?? '-' }}</p>
    <p><strong>Institución:</strong> {{ $alumno->institucion->nombre ?? '-' }}</p>
    <p><strong>Empresa:</strong> {{ $alumno->empresa->name ?? '-' }}</p>

    <h2>Asistencia</h2>
    <div class="chart">
        <img src="{{ $attendanceChartUrl }}" width="250" alt="Gráfica de asistencia">
    </div>
    <table>
        <thead>
            <tr><th>Fecha</th><th>Estado</th></tr>
        </thead>
        <tbody>
            @foreach($attendanceDetails as $dia)
                <tr>
                    <td>{{ $dia['fecha'] }}</td>
                    <td>{{ ucfirst($dia['estado']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Actividades</h2>
    <div class="chart">
        <img src="{{ $tasksChartUrl }}" width="250" alt="Gráfica de actividades">
    </div>
    <table>
        <thead>
            <tr><th>Actividad</th><th>Fecha de entrega</th></tr>
        </thead>
        <tbody>
            @foreach($entregasPeriodo as $entrega)
                <tr>
                    <td>{{ $entrega->subtema->nombre ?? 'Actividad' }}</td>
                    <td>{{ $entrega->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
