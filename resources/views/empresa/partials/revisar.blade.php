@php
    use App\Models\Empresa;
    use App\Models\Entrega;
    use Illuminate\Support\Facades\Auth;

    $empresa = Empresa::where('id_user', Auth::id())->first();
    $entregas = collect();
    if ($empresa) {
        $entregas = Entrega::with(['subtema.tema', 'alumno'])
            ->whereHas('alumno', function ($q) use ($empresa) {
                $q->where('id_empresa', $empresa->id);
            })
            ->where('estado', 'pen_emp')
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
                <ul class="flex flex-wrap gap-2 mt-1">
                    @foreach($entrega->subtema->rutas as $ruta)
                        @php $isImage = in_array(Str::lower(pathinfo($ruta, PATHINFO_EXTENSION)), ['jpg','jpeg','png','gif','bmp','webp']); @endphp
                        <li class="relative p-2 bg-gray-50 border rounded shadow-sm">
                            <a href="{{ asset('storage/'.$ruta) }}" target="_blank" class="block">
                                @if($isImage)
                                    <img src="{{ asset('storage/'.$ruta) }}" alt="archivo" class="w-20 h-20 object-contain rounded">
                                @else
                                    <svg class="w-20 h-20 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 2h6l5 5v13a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2z" />
                                    </svg>
                                @endif
                            </a>
                            <a href="{{ asset('storage/'.$ruta) }}" download class="absolute top-1 right-1 bg-blue-600 hover:bg-blue-700 text-white p-1 rounded-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v9m0 0l-3-3m3 3l3-3M4 19h16" />
                                </svg>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="mt-4 bg-green-50 border border-green-200 rounded p-3">
                <div class="font-semibold text-green-700 mb-1">Entrega de {{ $entrega->alumno->name }}</div>
                <div class="text-sm whitespace-pre-line">{{ $entrega->contenido }}</div>
                @if($entrega->rutas)
                    <ul class="flex flex-wrap gap-2 mt-2">
                        @foreach($entrega->rutas as $ruta)
                            @php $isImage = in_array(Str::lower(pathinfo($ruta, PATHINFO_EXTENSION)), ['jpg','jpeg','png','gif','bmp','webp']); @endphp
                            <li class="relative p-2 bg-gray-50 border rounded shadow-sm">
                                <a href="{{ asset('storage/'.$ruta) }}" target="_blank" class="block">
                                    @if($isImage)
                                        <img src="{{ asset('storage/'.$ruta) }}" alt="archivo" class="w-20 h-20 object-contain rounded">
                                    @else
                                        <svg class="w-20 h-20 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 2h6l5 5v13a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2z" />
                                        </svg>
                                    @endif
                                </a>
                                <a href="{{ asset('storage/'.$ruta) }}" download class="absolute top-1 right-1 bg-blue-600 hover:bg-blue-700 text-white p-1 rounded-full">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v9m0 0l-3-3m3 3l3-3M4 19h16" />
                                    </svg>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div x-data="{ accion: null }" class="mt-3 space-y-2">
                <div class="flex gap-2">
                    <button @click="accion = accion === 'verificar' ? null : 'verificar'" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Verificar</button>
                    <button @click="accion = accion === 'rechazar' ? null : 'rechazar'" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Rechazar</button>
                </div>
                <div x-show="accion === 'verificar'" x-cloak>
                    <form method="POST" action="{{ route('empresa.entregas.verificar', $entrega->id) }}" class="space-y-2">
                        @csrf
                        <textarea name="comentario" rows="2" class="w-full border rounded p-2" placeholder="Comentario opcional"></textarea>
                        <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Enviar</button>
                    </form>
                </div>
                <div x-show="accion === 'rechazar'" x-cloak>
                    <form method="POST" action="{{ route('empresa.entregas.rechazar', $entrega->id) }}" class="space-y-2">
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