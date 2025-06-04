@php
    use Illuminate\Support\Facades\Auth;
    use App\Models\Maestro;
    use App\Models\Plan;
    use App\Models\Alumno;

    $maestro = Maestro::where('id_user', Auth::id())->first();
    $planes = $maestro ? Plan::where('id_maestro', $maestro->id)->orderBy('nombre')->get() : collect();
    $alumnos = $maestro ? Alumno::where('id_maestro', $maestro->id)->whereNull('id_plan')->orderBy('name')->get() : collect();
@endphp

<div class="p-4 max-w-xl mx-auto">
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 mb-4 rounded shadow text-center">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="bg-red-100 text-red-800 p-2 mb-4 rounded shadow text-center">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('maestro.asignar_plan') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block font-semibold mb-1">Plan</label>
            <select name="plan" class="w-full border rounded p-2" required>
                <option value="">Selecciona un plan</option>
                @foreach($planes as $plan)
                    <option value="{{ $plan->id }}">{{ $plan->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block font-semibold mb-1">Alumnos sin plan</label>
            <div class="max-h-60 overflow-y-auto border rounded p-2 space-y-1">
                @forelse($alumnos as $alumno)
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="alumnos[]" value="{{ $alumno->id }}" class="rounded">
                        <span>{{ $alumno->name }}</span>
                    </label>
                @empty
                    <p class="text-gray-500 text-sm">No hay alumnos disponibles.</p>
                @endforelse
            </div>
        </div>

        @if($planes->count() && $alumnos->count())
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Asignar Plan</button>
        @endif
    </form>
</div>
