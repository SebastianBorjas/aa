@php
    use App\Models\Maestro;
    use App\Models\Entrega;
    use Illuminate\Support\Facades\Auth;

    $maestro = Maestro::where('id_user', Auth::id())->first();
    $entregas = collect();
    if ($maestro) {
        $entregas = Entrega::with(['subtema.tema', 'alumno'])
            ->whereHas('alumno', function ($q) use ($maestro) {
                $q->where('id_maestro', $maestro->id);
            })
            ->where('estado', 'pen_mae')
            ->orderByDesc('created_at')
            ->get();
    }
@endphp

<div class="space-y-6">
    @forelse($entregas as $entrega)
        <div class="border rounded-lg p-4 bg-gray-50">
            <h3 class="text-lg font-semibold text-blue-800">{{ $entrega->subtema->nombre }}</h3>
            @if($entrega->subtema->descripcion)
                <p class="text-sm text-gray-600 mb-2">{{ $entrega->subtema->descripcion }}</p>
            @endif
            @if($entrega->subtema->rutas)
                <ul class="list-disc ml-5 mt-1 text-sm">
                    @foreach($entrega->subtema->rutas as $ruta)
                        <li>
                            <a href="{{ asset('storage/'.$ruta) }}" target="_blank" class="flex items-center gap-1 text-blue-600 hover:underline max-w-[150px]">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 2h6l5 5v13a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2z" />
                                </svg>
                                <span class="truncate">{{ basename($ruta) }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="mt-4 bg-green-50 border border-green-200 rounded p-3">
                <div class="font-semibold text-green-700 mb-1">Entrega de {{ $entrega->alumno->name }}</div>
                <div class="text-sm whitespace-pre-line">{{ $entrega->contenido }}</div>
                @if($entrega->rutas)
                    <ul class="list-disc ml-5 mt-2 text-sm">
                        @foreach($entrega->rutas as $ruta)
                            <li>
                                <a href="{{ asset('storage/'.$ruta) }}" target="_blank" class="flex items-center gap-1 text-blue-600 hover:underline max-w-[150px]">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 2h6l5 5v13a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2z" />
                                    </svg>
                                    <span class="truncate">{{ basename($ruta) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
                @if($entrega->vce)
                    <div class="mt-2 p-2 bg-blue-50 border border-blue-300 rounded text-sm text-blue-700 whitespace-pre-line">
                        {{ $entrega->vce }}
                    </div>
                @endif
            </div>

            <div x-data="{ accion: null }" class="mt-3 space-y-2">
                <div class="flex gap-2">
                    <button @click="accion = accion === 'verificar' ? null : 'verificar'" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Verificar</button>
                    <button @click="accion = accion === 'rechazar' ? null : 'rechazar'" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Rechazar</button>
                </div>
                <div x-show="accion === 'verificar'" x-cloak>
                    <form method="POST" action="{{ route('maestro.entregas.verificar', $entrega->id) }}" class="space-y-2">
                        @csrf
                        <textarea name="comentario" rows="2" class="w-full border rounded p-2" placeholder="Comentario opcional"></textarea>
                        <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Enviar</button>
                    </form>
                </div>
                <div x-show="accion === 'rechazar'" x-cloak>
                    <form method="POST" action="{{ route('maestro.entregas.rechazar', $entrega->id) }}" class="space-y-2">
                        @csrf
                        <textarea name="comentario" rows="2" class="w-full border rounded p-2" placeholder="Comentario opcional"></textarea>
                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Enviar</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <p class="text-gray-600">No hay tareas por revisar.</p>
    @endforelse
</div>