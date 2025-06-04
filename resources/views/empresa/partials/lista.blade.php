@php
    use App\Models\Empresa;
    use App\Models\Alumno;
    use App\Models\Lista;
    use Illuminate\Support\Facades\Auth;
    use Carbon\Carbon;

    $empresa = Empresa::where('id_user', Auth::id())->first();
    $fechaInput = request('fecha', now()->toDateString());
    $fecha = Carbon::parse($fechaInput);
    if ($fecha->isFuture()) {
        $fecha = Carbon::now();
    }
    $diaColumn = ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'][$fecha->dayOfWeekIso - 1];

    $alumnos = $empresa
        ? Alumno::where('id_empresa', $empresa->id)
            ->where('fecha_inicio', '<=', $fecha->toDateString())
            ->where(function ($q) use ($fecha) {
                $q->whereNull('fecha_termino')->orWhere('fecha_termino', '>=', $fecha->toDateString());
            })
            ->where($diaColumn, true)
            ->orderBy('name')
            ->get()
        : collect();

    $registros = $empresa
        ? Lista::where('id_empresa', $empresa->id)
            ->where('fecha', $fecha->toDateString())
            ->get()
            ->keyBy('id_alumno')
        : collect();

    $minFecha = $empresa ? Alumno::where('id_empresa', $empresa->id)->min('fecha_inicio') : now()->toDateString();
    $maxCalc = $empresa ? Alumno::where('id_empresa', $empresa->id)->max('fecha_termino') : null;
    $maxFecha = Carbon::parse($maxCalc ?? now())->min(Carbon::now())->toDateString();
@endphp

<div class="space-y-4">
    <form method="GET" action="{{ route('empresa.inicio') }}" class="flex items-center space-x-2">
        <input type="hidden" name="tab" value="lista">
        <input type="date" name="fecha" value="{{ $fecha->toDateString() }}" min="{{ $minFecha }}" max="{{ $maxFecha }}" class="border rounded px-2 py-1">
        <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">Cambiar</button>
    </form>

    @if(session('success'))
        <div class="p-2 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-2 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
    @endif

    @if($alumnos->isEmpty())
        <p class="text-gray-600">No hay alumnos disponibles en esta fecha.</p>
    @else
        <form method="POST" action="{{ route('empresa.guardarLista') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="fecha" value="{{ $fecha->toDateString() }}">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-700 text-white">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium uppercase">Alumno</th>
                            <th class="px-3 py-2 text-center text-xs font-medium uppercase">Asistencia</th>
                            <th class="px-3 py-2 text-center text-xs font-medium uppercase">Falta</th>
                            <th class="px-3 py-2 text-center text-xs font-medium uppercase">Justificado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($alumnos as $alumno)
                            @php $estado = $registros[$alumno->id]->estado ?? 'asistencia'; @endphp
                            <tr>
                                <td class="px-3 py-2">{{ $alumno->name }}</td>
                                <td class="px-3 py-2 text-center">
                                    <input type="radio" name="estado[{{ $alumno->id }}]" value="asistencia" {{ $estado == 'asistencia' ? 'checked' : '' }}>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <input type="radio" name="estado[{{ $alumno->id }}]" value="falta" {{ $estado == 'falta' ? 'checked' : '' }}>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <input type="radio" name="estado[{{ $alumno->id }}]" value="justificado" {{ $estado == 'justificado' ? 'checked' : '' }}>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Guardar</button>
        </form>
    @endif
</div>