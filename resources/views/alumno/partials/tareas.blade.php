@php
    $plan = $alumno?->plan;
@endphp

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-2 mb-4 rounded shadow text-center">{{ session('success') }}</div>
@elseif(session('error'))
    <div class="bg-red-100 text-red-800 p-2 mb-4 rounded shadow text-center">{{ session('error') }}</div>
@endif

@if(!$plan)
    <div class="text-center text-gray-500">
        Solicita que se te asigne un plan.
    </div>
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
                            <ul class="list-disc ml-5 mt-1 text-sm">
                                @foreach($subtema->rutas as $ruta)
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

                        @php $entrega = $subtema->entregas->first(); @endphp

                        @if($entrega)
                            <div class="mt-2 bg-green-50 border border-green-200 rounded p-3">
                                <div class="font-semibold text-green-700 mb-1">
                                    Tu entrega
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
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                    @endphp
                                    <span class="ml-2 text-xs px-2 py-0.5 rounded {{ $estadoColor }}">{{ $estadoText }}</span>
                                </div>
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

                                @if($entrega->estado === 'rechazado' && ($entrega->rce || $entrega->rcm))
                                    <div class="mt-2 p-2 bg-red-50 border border-red-300 rounded text-sm text-red-700 whitespace-pre-line">
                                        {{ $entrega->rce ?? $entrega->rcm }}
                                    </div>
                                @endif
                            </div>
                            @if($entrega->estado === 'rechazado')
                                <div x-data="{ open: false, inputs: [1], add() { if(this.inputs.length < 4) this.inputs.push(Date.now()) }, remove(i){ this.inputs.splice(i,1) } }" class="mt-2">
                                    <button type="button" @click="open = !open" class="px-3 py-1 bg-green-600 text-white rounded shadow hover:bg-green-700">Reenviar tarea</button>
                                    <div x-show="open" x-cloak class="mt-2 border rounded p-3 bg-white">
                                        <form method="POST" action="{{ route('alumno.entregar_tarea', $subtema->id) }}" enctype="multipart/form-data" class="space-y-2">
                                            @csrf
                                            <textarea name="contenido" rows="3" class="w-full border rounded p-2" required>{{ $entrega->contenido }}</textarea>
                                            <template x-for="(input, index) in inputs" :key="input">
                                                <div class="flex items-center gap-2">
                                                    <input type="file" name="archivos[]" class="w-full border rounded p-2">
                                                    <button type="button" @click="remove(index)" class="text-red-600 text-sm">Eliminar</button>
                                                </div>
                                            </template>
                                            <button type="button" @click="add" x-show="inputs.length < 4" class="px-2 py-1 bg-gray-200 rounded text-sm">Agregar archivo</button>
                                            <p class="text-xs text-gray-500">Máx. 4 archivos, 2MB cada uno.</p>
                                            <button type="submit" class="px-4 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Enviar</button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div x-data="{ open: false, inputs: [1], add() { if(this.inputs.length < 4) this.inputs.push(Date.now()) }, remove(i){ this.inputs.splice(i,1) } }" class="mt-2">
                                <button type="button" @click="open = !open" class="px-3 py-1 bg-green-600 text-white rounded shadow hover:bg-green-700">Entregar tarea</button>
                                <div x-show="open" x-cloak class="mt-2 border rounded p-3 bg-white">
                                    <form method="POST" action="{{ route('alumno.entregar_tarea', $subtema->id) }}" enctype="multipart/form-data" class="space-y-2">
                                        @csrf
                                        <textarea name="contenido" rows="3" class="w-full border rounded p-2" placeholder="Contenido" required></textarea>
                                        <template x-for="(input, index) in inputs" :key="input">
                                            <div class="flex items-center gap-2">
                                                <input type="file" name="archivos[]" class="w-full border rounded p-2">
                                                <button type="button" @click="remove(index)" class="text-red-600 text-sm">Eliminar</button>
                                            </div>
                                        </template>
                                        <button type="button" @click="add" x-show="inputs.length < 4" class="px-2 py-1 bg-gray-200 rounded text-sm">Agregar archivo</button>
                                        <p class="text-xs text-gray-500">Máx. 4 archivos, 2MB cada uno.</p>
                                        <button type="submit" class="px-4 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Enviar</button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endif