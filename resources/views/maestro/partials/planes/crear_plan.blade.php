@php
    $planEditId = request('planEdit') ? intval(request('planEdit')) : null;
@endphp
<div x-data="{ planEdit: {{ $planEditId ?? 'null' }}, temaOpenId: null }" class="relative">

    {{-- MENSAJES --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-900 p-2 rounded mb-2 text-center shadow">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="bg-red-100 text-red-900 p-2 rounded mb-2 text-center shadow">{{ session('error') }}</div>
    @endif

    {{-- ZONA ÚNICA: Cards o Edición --}}
    <div class="max-w-2xl mx-auto">

        {{-- Vista: CARDS --}}
        <div x-show="!planEdit" class="flex flex-col gap-5">
            <form method="POST" action="{{ route('maestro.planes.store') }}" class="flex flex-col md:flex-row gap-3 items-end bg-white p-4 rounded-xl shadow mb-6">
                @csrf
                <div class="flex-1">
                    <label class="block text-gray-800 font-semibold mb-1">Nombre del plan</label>
                    <input type="text" name="nombre" required class="w-full border-2 border-blue-400 bg-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none font-semibold transition" placeholder="Ejemplo: Plan de Matemáticas">
                </div>
                <button type="submit" class="w-full md:w-auto px-6 py-2 bg-blue-600 text-white font-bold rounded-lg shadow hover:bg-blue-700 transition">
                    Crear plan
                </button>
            </form>
            @forelse($planes as $plan)
                <div 
                    class="bg-gradient-to-br from-blue-50 to-blue-100 border-l-4 border-blue-600 shadow-lg rounded-xl p-5 cursor-pointer hover:shadow-2xl hover:scale-[1.015] transition group"
                    @click="planEdit = {{ $plan->id }}"
                >
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="font-bold text-xl text-blue-900 group-hover:underline tracking-wide">{{ $plan->nombre }}</div>
                            <div class="text-xs text-gray-500">Creado el {{ $plan->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <svg class="w-7 h-7 text-blue-500 group-hover:text-blue-800 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-4">No tienes planes registrados.</div>
            @endforelse
        </div>

        {{-- Vista: EDICIÓN --}}
        @foreach($planes as $plan)
            <div 
                x-show="planEdit === {{ $plan->id }}"
                class="flex flex-col gap-6"
                style="display: none;"
            >
                {{-- Header --}}
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-3xl font-black text-blue-900 tracking-wide drop-shadow">Editar Plan</h2>
                    <button type="button" @click="planEdit = null; temaOpenId = null" class="bg-gray-200 px-5 py-2 rounded text-gray-700 hover:bg-gray-300 shadow font-bold">
                        Cerrar
                    </button>
                </div>
                {{-- Editar/eliminar plan --}}
                <form method="POST" action="{{ route('maestro.planes.update', $plan->id) }}" class="flex flex-col gap-3 items-center bg-white p-4 rounded-xl shadow border-2 border-blue-100 mb-3">
                    @csrf
                    <input type="text" name="nombre" value="{{ $plan->nombre }}" required class="w-full max-w-lg border-2 border-blue-400 bg-white rounded-lg px-3 py-2 text-xl font-semibold text-center outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Nombre del plan">
                    <button type="submit" class="px-7 py-2 bg-blue-600 text-white font-bold rounded-lg shadow hover:bg-blue-700 transition mt-2">
                        Guardar
                    </button>
                </form>
                <form method="POST" action="{{ route('maestro.planes.delete', $plan->id) }}" onsubmit="return confirm('¿Eliminar plan y todos sus temas?')" class="mb-5 text-center">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-8 py-2 rounded-lg shadow font-bold hover:bg-red-700 w-full sm:w-auto transition">Eliminar Plan</button>
                </form>

                {{-- Temas --}}
                <div class="flex flex-col gap-8">
                    @foreach($plan->temas as $tema)
                        <div class="bg-white border-2 border-blue-200 rounded-xl shadow-lg p-6 flex flex-col gap-2 relative hover:border-blue-400 transition">
                            {{-- Header Tema + Chevron --}}
                            <div class="flex items-center justify-between gap-2">
                                <button type="button"
                                    @click="temaOpenId === {{ $tema->id }} ? temaOpenId = null : temaOpenId = {{ $tema->id }}"
                                    class="flex items-center justify-center rounded-full w-8 h-8 hover:bg-blue-100 transition group"
                                    :aria-expanded="temaOpenId === {{ $tema->id }}"
                                    :aria-controls="'tema-{{ $tema->id }}-panel'">
                                    <svg :class="temaOpenId === {{ $tema->id }} ? 'rotate-90 text-blue-800' : 'text-blue-400'" class="w-6 h-6 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <form method="POST" action="{{ route('maestro.temas.save') }}" class="flex-1 flex flex-col items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $tema->id }}">
                                    <input type="hidden" name="id_plan" value="{{ $plan->id }}">
                                    <input type="text" name="nombre" value="{{ $tema->nombre }}"
                                        placeholder="Nombre del tema"
                                        class="block mx-auto border-2 border-blue-400 rounded px-3 py-1 font-bold max-w-xs text-center text-blue-900 text-lg outline-none focus:ring-2 focus:ring-blue-400 transition" required>
                                    <textarea name="descripcion" rows="4" placeholder="Descripción (opcional)"
                                        class="w-full border-2 border-blue-200 rounded px-3 py-2 h-24 outline-none focus:ring-2 focus:ring-blue-300 transition"
                                        style="resize: none;">{{ $tema->descripcion }}</textarea>
                                    <button type="submit" class="bg-blue-500 text-white font-bold px-4 py-1 rounded shadow hover:bg-blue-600 transition mt-2">Guardar</button>
                                </form>
                                <form method="POST" action="{{ route('maestro.temas.delete', $tema->id) }}" onsubmit="return confirm('¿Eliminar este tema y sus subtemas?')" class="flex-none self-center">
                                    @csrf
                                    <button type="submit" class="bg-red-500 text-white font-bold px-3 py-1 rounded shadow hover:bg-red-600 transition">Eliminar</button>
                                </form>
                            </div>
                            {{-- SUBTEMAS plegables --}}
                            <div x-show="temaOpenId === {{ $tema->id }}"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95" 
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 id="tema-{{ $tema->id }}-panel"
                                 class="mt-5">
                                <div class="text-blue-800 font-semibold text-sm mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                    Subtemas:
                                </div>
                                <div class="flex flex-col gap-4">
                                    @foreach($tema->subtemas as $subtema)
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex flex-col items-center gap-2 shadow hover:border-blue-400 transition relative">
                                            <form method="POST" action="{{ route('maestro.subtemas.save') }}" class="flex flex-col items-center gap-2 w-full">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $subtema->id }}">
                                                <input type="hidden" name="id_tema" value="{{ $tema->id }}">
                                                <input type="text" name="nombre" value="{{ $subtema->nombre }}"
                                                    placeholder="Nombre subtema"
                                                    class="block mx-auto border-2 border-blue-300 rounded px-3 py-1 font-medium max-w-xs text-center outline-none focus:ring-2 focus:ring-blue-400 transition" required>
                                                <textarea name="descripcion" rows="4" placeholder="Descripción (opcional)"
                                                    class="w-full border-2 border-blue-200 rounded px-3 py-2 h-24 outline-none focus:ring-2 focus:ring-blue-300 transition"
                                                    style="resize: none;">{{ $subtema->descripcion }}</textarea>
                                                <button type="submit" class="bg-blue-400 text-white font-bold px-4 py-1 rounded shadow hover:bg-blue-600 transition mt-2">Guardar</button>
                                            </form>
                                            <form method="POST" action="{{ route('maestro.subtemas.delete', $subtema->id) }}" onsubmit="return confirm('¿Eliminar subtema?')" class="absolute top-3 right-3">
                                                @csrf
                                                <button type="submit" class="bg-red-400 text-white font-bold px-3 py-1 rounded shadow hover:bg-red-600 transition">Eliminar</button>
                                            </form>
                                        </div>
                                    @endforeach
                                    {{-- Agregar subtema --}}
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex flex-col items-center gap-2 shadow">
                                        <form method="POST" action="{{ route('maestro.subtemas.save') }}" class="flex flex-col items-center gap-2 w-full">
                                            @csrf
                                            <input type="hidden" name="id_tema" value="{{ $tema->id }}">
                                            <input type="text" name="nombre" placeholder="Nuevo subtema"
                                                class="block mx-auto border-2 border-blue-300 rounded px-3 py-1 max-w-xs text-center outline-none focus:ring-2 focus:ring-blue-400 transition" required>
                                            <textarea name="descripcion" rows="4" placeholder="Descripción"
                                                class="w-full border-2 border-blue-200 rounded px-3 py-2 h-24 outline-none focus:ring-2 focus:ring-blue-300 transition"
                                                style="resize: none;"></textarea>
                                            <button type="submit" class="bg-green-500 text-white font-bold px-4 py-1 rounded shadow hover:bg-green-700 transition mt-2">Agregar Subtema</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    {{-- Agregar tema --}}
                    <div class="bg-green-50 border-2 border-green-300 p-6 rounded-xl shadow flex flex-col items-center gap-2 mt-2">
                        <form method="POST" action="{{ route('maestro.temas.save') }}" class="flex flex-col items-center gap-2 w-full">
                            @csrf
                            <input type="hidden" name="id_plan" value="{{ $plan->id }}">
                            <input type="text" name="nombre" placeholder="Nuevo tema"
                                class="block mx-auto border-2 border-green-400 rounded px-3 py-1 max-w-xs text-center outline-none focus:ring-2 focus:ring-green-400 transition" required>
                            <textarea name="descripcion" rows="4" placeholder="Descripción"
                                class="w-full border-2 border-green-300 rounded px-3 py-2 h-24 outline-none focus:ring-2 focus:ring-green-400 transition"
                                style="resize: none;"></textarea>
                            <button type="submit" class="bg-green-600 text-white font-bold px-4 py-1 rounded shadow hover:bg-green-700 transition mt-2">Agregar Tema</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
        </div>
    </div>
</div>
