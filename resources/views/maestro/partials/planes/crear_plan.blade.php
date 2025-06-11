@php
    $planEditId = request('planEdit') ? intval(request('planEdit')) : null;
@endphp
<div x-data="{ planEdit: {{ $planEditId ?? 'null' }}, temaOpenId: null, showNewTema: false }" class="relative">

    {{-- MENSAJES --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-900 p-2 rounded mb-2 text-center shadow">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="bg-red-100 text-red-900 p-2 rounded mb-2 text-center shadow">{{ session('error') }}</div>
    @endif

    <div class="max-w-4xl mx-auto w-full px-2">

        {{-- Vista: CARDS --}}
        <div x-show="!planEdit" class="flex flex-col gap-5">
            <form method="POST" action="{{ route('maestro.planes.store') }}" class="flex flex-col md:flex-row gap-3 items-end bg-white p-4 rounded-xl shadow mb-6">
                @csrf
                <div class="flex-1 w-full">
                    <label class="block text-gray-800 font-semibold mb-1">Nombre del plan</label>
                    <input type="text" name="nombre" required class="w-full border-2 border-blue-400 bg-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none font-semibold transition" placeholder="Ejemplo: Plan de Matemáticas">
                </div>
                <button type="submit" class="w-full md:w-auto px-6 py-2 bg-blue-600 text-white font-bold rounded-lg shadow hover:bg-blue-700 transition">
                    Crear plan
                </button>
            </form>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($planes as $plan)
                    <a
                        href="{{ route('maestro.planes.ver', $plan->id) }}"
                        class="block bg-gray-50 border border-gray-300 rounded-lg p-4 shadow-sm hover:shadow-md transition-transform transform hover:-translate-y-1 group"
                    >
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="font-semibold text-gray-900 group-hover:text-blue-800">{{ $plan->nombre }}</div>
                                <div class="text-xs text-gray-500 mt-1">Creado el {{ $plan->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <svg class="w-5 h-5 text-blue-700 group-hover:text-blue-900 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>
                @empty
                    <div class="text-center text-gray-500 py-4 sm:col-span-2 lg:col-span-3">No tienes planes registrados.</div>
                @endforelse
            </div>
        </div>

        {{-- Vista: EDICIÓN --}}
        @foreach($planes as $plan)
        <div 
            x-show="planEdit === {{ $plan->id }}"
            class="flex flex-col gap-6"
            style="display: none;"
        >
            <div class="flex flex-wrap items-center justify-between mb-4 gap-2">
                <h2 class="text-2xl font-black text-blue-900 tracking-wide drop-shadow">Plan</h2>
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
                <div class="border rounded-lg p-4 bg-gray-50 flex flex-col gap-4">
                    <div class="flex items-center justify-between gap-2 flex-wrap">
                        <button type="button"
                            @click="temaOpenId === {{ $tema->id }} ? temaOpenId = null : temaOpenId = {{ $tema->id }}"
                            class="flex items-center justify-center rounded-full w-8 h-8 hover:bg-blue-100 transition group"
                            :aria-expanded="temaOpenId === {{ $tema->id }}"
                            :aria-controls="'tema-{{ $tema->id }}-panel'">
                            <svg :class="temaOpenId === {{ $tema->id }} ? 'rotate-90 text-blue-800' : 'text-blue-400'" class="w-6 h-6 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <form method="POST" action="{{ route('maestro.temas.save') }}" class="flex-1 flex flex-col items-center gap-2 min-w-[160px]">
                            @csrf
                            <input type="hidden" name="id" value="{{ $tema->id }}">
                            <input type="hidden" name="id_plan" value="{{ $plan->id }}">
                            <input type="text" name="nombre" value="{{ $tema->nombre }}"
                                placeholder="Nombre del tema"
                                class="block mx-auto border-2 border-blue-400 rounded px-3 py-1 font-bold max-w-xs text-center text-blue-900 text-lg outline-none focus:ring-2 focus:ring-blue-400 transition" required>
                            <textarea name="descripcion" rows="2" placeholder="Descripción (opcional)"
                                class="w-full border-2 border-blue-200 rounded px-3 py-2 outline-none focus:ring-2 focus:ring-blue-300 transition resize-none"
                                >{{ $tema->descripcion }}</textarea>
                            <button type="submit" class="bg-blue-500 text-white font-bold px-4 py-1 rounded shadow hover:bg-blue-600 transition mt-2">Guardar</button>
                        </form>
                        <form method="POST" action="{{ route('maestro.temas.delete', $tema->id) }}" onsubmit="return confirm('¿Eliminar este tema y sus subtemas?')" class="flex-none self-center ml-2">
                            @csrf
                            <button type="submit" class="bg-red-500 text-white font-bold px-3 py-1 rounded shadow hover:bg-red-600 transition">Eliminar</button>
                        </form>
                    </div>
                    {{-- SUBTEMAS --}}
                    <div x-show="temaOpenId === {{ $tema->id }}"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" 
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        id="tema-{{ $tema->id }}-panel"
                        class="flex flex-col gap-5 mt-3">

                        {{-- SUBTEMAS --}}
                        @foreach($tema->subtemas as $subtema)
                        <div class="mt-3 pl-4 border-l-4 border-blue-300">
                            {{-- Título y eliminar --}}
                            <div class="flex items-center justify-between mb-1">
                                <div class="font-bold text-blue-900 text-base">Subtema</div>
                                <form method="POST" action="{{ route('maestro.subtemas.delete', $subtema->id) }}" onsubmit="return confirm('¿Eliminar subtema?')">
                                    @csrf
                                    <button type="submit" class="bg-red-400 text-white font-bold px-3 py-1 rounded shadow hover:bg-red-600 transition">Eliminar</button>
                                </form>
                            </div>
                            {{-- Form editar --}}
                            <form method="POST" action="{{ route('maestro.subtemas.save') }}" class="flex flex-col items-center gap-2 w-full">
                                @csrf
                                <input type="hidden" name="id" value="{{ $subtema->id }}">
                                <input type="hidden" name="id_tema" value="{{ $tema->id }}">
                                <input type="text" name="nombre" value="{{ $subtema->nombre }}"
                                    placeholder="Nombre subtema"
                                    class="block mx-auto border-2 border-blue-300 rounded px-3 py-1 font-medium max-w-xs text-center outline-none focus:ring-2 focus:ring-blue-400 transition" required>
                                <textarea name="descripcion" rows="2" placeholder="Descripción (opcional)"
                                    class="w-full border-2 border-blue-200 rounded px-3 py-2 outline-none focus:ring-2 focus:ring-blue-300 transition resize-none"
                                >{{ $subtema->descripcion }}</textarea>
                                <button type="submit" class="bg-blue-400 text-white font-bold px-4 py-1 rounded shadow hover:bg-blue-600 transition mt-2">Guardar</button>
                            </form>
                            {{-- ARCHIVOS --}}
                            <div class="mt-3 w-full">
                                <div class="font-semibold text-blue-700 mb-1">Archivos:</div>
                                <div class="flex flex-wrap gap-2 mb-1">
                                    @if($subtema->rutas)
                                        @foreach($subtema->rutas as $i => $ruta)
                                            <div class="flex items-center gap-1 bg-blue-100 rounded px-2 py-1">
                                                <a href="{{ asset('storage/'.$ruta) }}" target="_blank" class="flex items-center gap-1 text-blue-600 hover:underline text-xs max-w-[90px]">
                                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 2h6l5 5v13a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2z" />
                                                    </svg>
                                                    <span class="truncate">{{ basename($ruta) }}</span>
                                                </a>
                                                <form method="POST" action="{{ route('maestro.subtemas.deletefile', $subtema->id) }}" onsubmit="return confirm('¿Eliminar este archivo?')">
                                                    @csrf
                                                    <input type="hidden" name="file_index" value="{{ $i }}">
                                                    <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-xs" title="Eliminar archivo">&times;</button>
                                                </form>
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-gray-500 text-sm">No hay archivos subidos.</span>
                                    @endif
                                </div>
                                @if(count($subtema->rutas ?? []) < 4)
                                <div x-data="fileUploader()" class="mt-1">
                                    <button type="button" @click="open = !open" class="px-3 py-1 bg-green-600 text-white rounded shadow hover:bg-green-700">Subir archivo</button>
                                    <div x-show="open" x-cloak class="mt-2 border rounded p-3 bg-white">
                                        <form method="POST" action="{{ route('maestro.subtemas.addfile', $subtema->id) }}" enctype="multipart/form-data" class="space-y-2">
                                            @csrf
                                            <div x-ref="inputs"></div>
                                            <input type="file" multiple class="hidden" x-ref="fileInput" @change="handleFiles">
                                            <template x-for="(file, index) in files" :key="file.id">
                                                <div class="flex items-center gap-2">
                                                    <span class="truncate w-full" x-text="file.name"></span>
                                                    <button type="button" @click="removeFile(index)" class="text-red-600 text-sm">Eliminar</button>
                                                </div>
                                            </template>
                                            <button type="button" @click="openPicker" x-show="files.length < 4" class="px-2 py-1 bg-gray-200 rounded text-sm">Agregar archivos</button>
                                            <p class="text-xs text-gray-500">Máx. 4 archivos, 2MB cada uno.</p>
                                            <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Subir</button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach

                        {{-- AGREGAR SUBTEMA: BOTÓN Y FORM --}}
                        <div x-data="{ showNewSubtema: false }" class="mt-2">
                            <button type="button"
                                @click="showNewSubtema = !showNewSubtema"
                                class="flex items-center gap-1 px-4 py-2 bg-green-100 text-green-800 font-semibold rounded-lg shadow hover:bg-green-200 transition w-full">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                Agregar Subtema
                            </button>
                            <div x-show="showNewSubtema" x-transition class="mt-2 bg-white border-2 border-green-200 rounded-lg shadow p-4">
                                <form method="POST" action="{{ route('maestro.subtemas.save') }}" class="flex flex-col items-center gap-2 w-full">
                                    @csrf
                                    <input type="hidden" name="id_tema" value="{{ $tema->id }}">
                                    <input type="text" name="nombre" placeholder="Nuevo subtema"
                                        class="block mx-auto border-2 border-green-400 rounded px-3 py-1 max-w-xs text-center outline-none focus:ring-2 focus:ring-green-400 transition" required>
                                    <textarea name="descripcion" rows="2" placeholder="Descripción"
                                        class="w-full border-2 border-green-200 rounded px-3 py-2 outline-none focus:ring-2 focus:ring-green-200 transition resize-none"></textarea>
                                    <button type="submit" class="bg-green-500 text-white font-bold px-4 py-1 rounded shadow hover:bg-green-700 transition mt-2">Agregar Subtema</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- AGREGAR TEMA: BOTÓN Y FORM --}}
                <div x-data="{ showNewTema: false }" class="mt-2">
                    <button type="button"
                        @click="showNewTema = !showNewTema"
                        class="flex items-center gap-1 px-4 py-2 bg-green-100 text-green-800 font-semibold rounded-lg shadow hover:bg-green-200 transition w-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agregar Tema
                    </button>
                    <div x-show="showNewTema" x-transition class="mt-2 bg-white border-2 border-green-200 rounded-lg shadow p-4">
                        <form method="POST" action="{{ route('maestro.temas.save') }}" class="flex flex-col items-center gap-2 w-full">
                            @csrf
                            <input type="hidden" name="id_plan" value="{{ $plan->id }}">
                            <input type="text" name="nombre" placeholder="Nuevo tema"
                                class="block mx-auto border-2 border-green-400 rounded px-3 py-1 max-w-xs text-center outline-none focus:ring-2 focus:ring-green-400 transition" required>
                            <textarea name="descripcion" rows="2" placeholder="Descripción"
                                class="w-full border-2 border-green-300 rounded px-3 py-2 outline-none focus:ring-2 focus:ring-green-400 transition resize-none"></textarea>
                            <button type="submit" class="bg-green-600 text-white font-bold px-4 py-1 rounded shadow hover:bg-green-700 transition mt-2">Agregar Tema</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@push('vite')
<script>
function fileUploader() {
    return {
        open: false,
        files: [],
        openPicker() {
            this.$refs.fileInput.click();
        },
        handleFiles(e) {
            for (const file of Array.from(e.target.files)) {
                if (this.files.length >= 4) break;
                const id = Date.now() + Math.random();
                const dt = new DataTransfer();
                dt.items.add(file);
                const input = document.createElement('input');
                input.type = 'file';
                input.name = 'archivos[]';
                input.classList.add('hidden');
                input.files = dt.files;
                input.dataset.id = id;
                this.$refs.inputs.appendChild(input);
                this.files.push({id, name: file.name});
            }
            e.target.value = '';
        },
        removeFile(index) {
            const removed = this.files.splice(index, 1)[0];
            const el = this.$refs.inputs.querySelector('input[data-id="'+removed.id+'"]');
            if (el) el.remove();
        }
    }
}
</script>
@endpush