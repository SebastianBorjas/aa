@php
    $plan = $alumno?->plan;
@endphp

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
                    @php $entrega = $subtema->entregas->first(); @endphp
                    <div class="mt-3 pl-4 border-l-4 border-blue-300" x-data="{ open: false }">
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

                        @if($entrega)
                            <div class="mt-2 bg-green-50 border-l-4 border-green-500 p-2">
                                <p class="text-sm font-semibold text-green-700">Tu entrega</p>
                                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $entrega->contenido }}</p>
                                @if($entrega->rutas)
                                    <ul class="list-disc ml-5 mt-1 text-sm">
                                        @foreach($entrega->rutas as $eruta)
                                            <li>
                                                <a href="{{ asset('storage/'.$eruta) }}" target="_blank" class="flex items-center gap-1 text-blue-600 hover:underline max-w-[150px]">
                                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 2h6l5 5v13a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2z" />
                                                    </svg>
                                                    <span class="truncate">{{ basename($eruta) }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @else
                            <div class="mt-2" x-data="{ open: false }">
                                <button type="button" @click="open = !open" class="text-sm text-blue-600 underline">Entregar tarea</button>
                                <form x-show="open" x-transition method="POST" action="{{ route('alumno.subtemas.entregar', $subtema->id) }}" enctype="multipart/form-data" class="mt-2 space-y-2">
                                    @csrf
                                    <textarea name="contenido" rows="3" class="w-full border rounded p-2" required></textarea>
                                    <input type="file" name="archivos[]" multiple class="w-full border" accept="*">
                                    <p class="text-xs text-gray-500">Máx. 4 archivos, 2MB cada uno.</p>
                                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Enviar</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endif

