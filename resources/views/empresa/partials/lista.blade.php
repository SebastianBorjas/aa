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

<div class="space-y-4 max-w-3xl mx-auto">
    <form method="GET" action="{{ route('empresa.inicio') }}" class="flex items-center space-x-2">
        <input type="hidden" name="tab" value="lista">
        <input type="date" name="fecha" value="{{ $fecha->toDateString() }}" min="{{ $minFecha }}" max="{{ $maxFecha }}" class="border rounded px-2 py-1" onchange="this.form.submit()">
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
            <div class="space-y-2 sm:space-y-0">
                <div class="sm:hidden space-y-2">
                    @foreach($alumnos as $alumno)
                        @php
                            $registro = $registros[$alumno->id] ?? null;
                            $estado = $registro->estado ?? 'asistencia';
                            $registrado = !is_null($registro);
                        @endphp
                        <div class="p-2 rounded {{ $registrado ? 'bg-blue-200 border border-blue-600 text-blue-900' : 'bg-green-200 border border-green-600 text-green-900' }}">
                            <div class="flex items-center gap-2">
                                @if($registrado)
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        <circle cx="12" cy="12" r="9" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m4-4H8" />
                                        <circle cx="12" cy="12" r="9" />
                                    </svg>
                                @endif
                                <span class="font-semibold flex-1">{{ $alumno->name }}</span>
                            </div>
                            <div class="mt-2 flex justify-around">
                                <label class="inline-flex items-center justify-center">
                                    <input type="radio" name="estado[{{ $alumno->id }}]" value="asistencia" class="sr-only peer" {{ $estado == 'asistencia' ? 'checked' : '' }}>
                                    <span class="w-6 h-6 flex items-center justify-center rounded-full border cursor-pointer text-sm font-semibold transition {{ $registrado ? 'peer-checked:bg-blue-600' : 'peer-checked:bg-green-600' }} peer-checked:text-white">✓</span>
                                </label>
                                <label class="inline-flex items-center justify-center">
                                    <input type="radio" name="estado[{{ $alumno->id }}]" value="falta" class="sr-only peer" {{ $estado == 'falta' ? 'checked' : '' }}>
                                    <span class="w-6 h-6 flex items-center justify-center rounded-full border cursor-pointer text-sm font-semibold transition {{ $registrado ? 'peer-checked:bg-blue-600' : 'peer-checked:bg-green-600' }} peer-checked:text-white">✕</span>
                                </label>
                                <label class="inline-flex items-center justify-center">
                                    <input type="radio" name="estado[{{ $alumno->id }}]" value="justificado" class="sr-only peer" {{ $estado == 'justificado' ? 'checked' : '' }}>
                                    <span class="w-6 h-6 flex items-center justify-center rounded-full border cursor-pointer text-sm font-semibold transition {{ $registrado ? 'peer-checked:bg-blue-600' : 'peer-checked:bg-green-600' }} peer-checked:text-white">J</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="hidden sm:block overflow-x-auto">
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
                                @php
                                    $registro = $registros[$alumno->id] ?? null;
                                    $estado = $registro->estado ?? 'asistencia';
                                    $registrado = !is_null($registro);
                                @endphp
                                <tr class="{{ $registrado ? 'bg-blue-200 border border-blue-600 text-blue-900' : 'bg-green-200 border border-green-600 text-green-900' }}">
                                    <td class="px-3 py-2 flex items-center gap-2">
                                        @if($registrado)
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                <circle cx="12" cy="12" r="9" />
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m4-4H8" />
                                                <circle cx="12" cy="12" r="9" />
                                            </svg>
                                        @endif
                                        <span>{{ $alumno->name }}</span>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <label class="inline-flex items-center justify-center">
                                            <input type="radio" name="estado[{{ $alumno->id }}]" value="asistencia" class="sr-only peer" {{ $estado == 'asistencia' ? 'checked' : '' }}>
                                            <span class="w-6 h-6 flex items-center justify-center rounded-full border cursor-pointer text-sm font-semibold transition {{ $registrado ? 'peer-checked:bg-blue-600' : 'peer-checked:bg-green-600' }} peer-checked:text-white">✓</span>
                                        </label>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <label class="inline-flex items-center justify-center">
                                            <input type="radio" name="estado[{{ $alumno->id }}]" value="falta" class="sr-only peer" {{ $estado == 'falta' ? 'checked' : '' }}>
                                            <span class="w-6 h-6 flex items-center justify-center rounded-full border cursor-pointer text-sm font-semibold transition {{ $registrado ? 'peer-checked:bg-blue-600' : 'peer-checked:bg-green-600' }} peer-checked:text-white">✕</span>
                                        </label>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <label class="inline-flex items-center justify-center">
                                            <input type="radio" name="estado[{{ $alumno->id }}]" value="justificado" class="sr-only peer" {{ $estado == 'justificado' ? 'checked' : '' }}>
                                            <span class="w-6 h-6 flex items-center justify-center rounded-full border cursor-pointer text-sm font-semibold transition {{ $registrado ? 'peer-checked:bg-blue-600' : 'peer-checked:bg-green-600' }} peer-checked:text-white">J</span>
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Guardar</button>
        </form>
    @endif
</div>