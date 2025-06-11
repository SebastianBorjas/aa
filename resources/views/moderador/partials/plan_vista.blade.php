@php
    $plan = $alumno->plan;
@endphp
@if(!$plan)
    <div class="text-center text-gray-500">Sin plan asignado.</div>
@else
    <div class="space-y-6">
        <h2 class="text-center text-2xl font-bold text-blue-900">{{ $plan->nombre }}</h2>
        @foreach($plan->temas as $tema)
            <div class="border rounded-lg p-4 bg-gray-50">
                <h3 class="text-lg font-semibold text-blue-800">{{ $tema->nombre }}</h3>
                @if($tema->descripcion)
                    <p class="text-sm text-gray-600 mb-2">{{ $tema->descripcion }}</p>
                @endif
                @foreach($tema->subtemas as $subtema)
                    <div class="mt-3 pl-4 border-l-4 border-blue-300">
                        <h4 class="font-medium text-blue-700">{{ $subtema->nombre }}</h4>
                        @if($subtema->descripcion)
                            <p class="text-sm text-gray-600">{{ $subtema->descripcion }}</p>
                        @endif
                        @if($subtema->rutas)
                            <ul class="flex flex-wrap gap-2 mt-1">
                                @foreach($subtema->rutas as $ruta)
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
                        @php $entrega = $alumno->entregas->firstWhere('id_subtema', $subtema->id); @endphp
                        @if($entrega)
                            <div class="mt-2 bg-green-50 border border-green-200 rounded p-3">
                                <div class="font-semibold text-green-700 mb-1">
                                    Entrega del alumno
                                    @php
                                        $estadoText = [
                                            'pen_emp' => 'pendiente empresa',
                                            'pen_mae' => 'pendiente maestro',
                                            'verificado' => 'verificado',
                                            'rechazado' => 'rechazado',
                                        ][$entrega->estado] ?? $entrega->estado;
                                        $estadoColor = match($entrega->estado) {
                                            'pen_emp' => 'bg-yellow-100 text-yellow-800',
                                            'pen_mae' => 'bg-blue-100 text-blue-800',
                                            'verificado' => 'bg-green-100 text-green-800',
                                            'rechazado' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="ml-2 text-xs px-2 py-0.5 rounded {{ $estadoColor }}">{{ $estadoText }}</span>
                                </div>
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
                                @if($entrega->estado === 'rechazado' && ($entrega->rce || $entrega->rcm))
                                    <div class="mt-2 p-2 bg-red-50 border border-red-300 rounded text-sm text-red-700 whitespace-pre-line">
                                        <strong>{{ $entrega->rce ? 'Rechazado por empresa:' : 'Rechazado por maestro:' }}</strong>
                                        <br>{{ $entrega->rce ?? $entrega->rcm }}
                                    </div>
                                @endif
                                @if($entrega->estado === 'verificado' && $entrega->vcm)
                                    <div class="mt-2 p-2 bg-blue-50 border border-blue-300 rounded text-sm text-blue-700 whitespace-pre-line">
                                        <strong>Comentario del maestro:</strong><br>
                                        {{ $entrega->vcm }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endif