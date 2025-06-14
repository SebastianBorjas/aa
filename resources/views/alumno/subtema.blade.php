@extends('layouts.base2')

@push('vite')
  @vite('resources/css/app.css')
  <script src="https://unpkg.com/alpinejs" defer></script>
@endpush

@section('title', 'Subtema')

@section('main')
<div x-data="{ sidebarOpen: false }" class="flex flex-col md:flex-row flex-grow relative md:pl-64">
  <!-- Hamburger Button (Mobile Only) -->
  <button x-show="!sidebarOpen" @click="sidebarOpen = true" class="md:hidden fixed top-4 left-4 z-50 p-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
    </svg>
  </button>

  <!-- Sidebar (Desktop) -->
  <aside class="hidden md:block md:fixed md:left-0 md:top-16 md:h-[calc(100vh_-_4rem)] w-64 bg-[#202c54] text-white p-4 space-y-4 overflow-y-auto">
    <nav class="flex flex-col gap-2">
      <a href="{{ route('alumno.inicio', ['tab' => 'tareas']) }}" class="px-4 py-2 rounded bg-[#2e3a68] text-white hover:bg-[#2e3a68] transition text-left font-medium">Tareas</a>
      <a href="{{ route('alumno.inicio', ['tab' => 'informacion']) }}" class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium">Información</a>
    </nav>
    <form method="POST" action="{{ route('logout') }}" class="pt-4 border-t border-white/20">
      @csrf
      <button type="submit" class="w-full mt-2 px-4 py-2 bg-red-600 hover:bg-red-700 rounded text-white font-semibold transition">Cerrar sesión</button>
    </form>
  </aside>

  <!-- Mobile Sidebar and Overlay -->
  <div x-show="sidebarOpen" x-cloak class="md:hidden">
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-40" @click="sidebarOpen = false"></div>
    <aside x-transition:enter="transition ease-out duration-300" x-transition:enter-start="transform -translate-x-full" x-transition:enter-end="transform translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="transform translate-x-0" x-transition:leave-end="transform -translate-x-full" class="fixed left-0 top-0 w-64 bg-[#202c54] text-white p-4 h-full z-50">
      <button @click="sidebarOpen = false" class="mb-4 p-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
      <nav class="flex flex-col gap-2">
        <a href="{{ route('alumno.inicio', ['tab' => 'tareas']) }}" @click="sidebarOpen = false" class="px-4 py-2 rounded bg-[#2e3a68] text-white hover:bg-[#2e3a68] transition text-left font-medium">Tareas</a>
        <a href="{{ route('alumno.inicio', ['tab' => 'informacion']) }}" @click="sidebarOpen = false" class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium">Información</a>
      </nav>
      <form method="POST" action="{{ route('logout') }}" class="pt-4 border-t border-white/20 mt-auto">
        @csrf
        <button type="submit" class="w-full mt-2 px-4 py-2 bg-red-600 hover:bg-red-700 rounded text-white font-semibold transition">Cerrar sesión</button>
      </form>
    </aside>
  </div>

  <main class="flex-grow bg-white p-6">
    <div class="mb-4">
      <a href="{{ route('alumno.tema', $subtema->id_tema) }}" class="text-blue-600 hover:underline">&larr; Volver a los subtemas</a>
    </div>
    @php $entrega = $subtema->entregas->first(); @endphp
    <div class="max-w-3xl mx-auto space-y-6">
      <div class="bg-white border border-gray-300 rounded-lg shadow p-6 text-center">
        <h2 class="text-2xl font-bold text-blue-900">{{ $subtema->nombre }}</h2>
        @if($subtema->descripcion)
          <p class="mt-2 text-gray-700 whitespace-pre-line">{{ $subtema->descripcion }}</p>
        @endif
        @if($subtema->rutas)
          <ul class="list-disc mt-2 text-sm text-left inline-block">
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
      </div>
      <div class="space-y-4">
        @if($entrega)
          <div class="bg-green-50 border border-green-200 rounded p-4 text-center max-w-2xl mx-auto">
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
              <ul class="list-disc ml-5 mt-2 text-sm text-left">
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
                <strong>{{ $entrega->rce ? 'Rechazado por empresa:' : 'Rechazado por maestro:' }}</strong>
                <br>{{ $entrega->rce ?? $entrega->rcm }}
              </div>
            @endif
            @if($entrega->estado === 'verificado' && $entrega->vcm)
              <div class="mt-2 p-2 bg-blue-50 border border-blue-300 rounded text-sm text-blue-700 whitespace-pre-line">
                <strong>Comentario del maestro:</strong><br>{{ $entrega->vcm }}
              </div>
            @endif
          </div>
          @if($entrega->estado === 'rechazado')
            <div class="bg-white border rounded p-4 shadow max-w-2xl mx-auto" x-data="fileUploader()">
              <form method="POST" action="{{ route('alumno.entregar_tarea', $subtema->id) }}" enctype="multipart/form-data" class="space-y-2">
                @csrf
                <textarea name="contenido" rows="3" class="w-full border rounded p-2" required>{{ $entrega->contenido }}</textarea>
                @if($entrega->rutas)
                  <ul class="list-disc ml-5 text-sm space-y-1 text-left">
                    @foreach($entrega->rutas as $idx => $ruta)
                      <li class="flex items-center gap-2">
                        <a href="{{ asset('storage/'.$ruta) }}" target="_blank" class="flex items-center gap-1 text-blue-600 hover:underline max-w-[150px]">
                          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 2h6l5 5v13a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2z" />
                          </svg>
                          <span class="truncate">{{ basename($ruta) }}</span>
                        </a>
                        <label class="flex items-center gap-1 text-red-600 text-xs">
                          <input type="checkbox" name="delete_files[]" value="{{ $idx }}">Eliminar
                        </label>
                      </li>
                    @endforeach
                  </ul>
                @endif
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
                <button type="submit" class="px-4 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Enviar</button>
              </form>
            </div>
          @endif
        @else
          <div class="bg-white border rounded p-4 shadow max-w-2xl mx-auto" x-data="fileUploader()">
            <form method="POST" action="{{ route('alumno.entregar_tarea', $subtema->id) }}" enctype="multipart/form-data" class="space-y-2">
              @csrf
              <textarea name="contenido" rows="3" class="w-full border rounded p-2" placeholder="Contenido" required></textarea>
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
              <button type="submit" class="px-4 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Enviar</button>
            </form>  
          </div>
        @endif
      </div>
    </div>
  </main>
</div>

<style>
  [x-cloak] { display: none; }
  main { position: relative; z-index: 20; }
</style>
@endsection

@push('vite')
<script>
function fileUploader() {
    return {
        files: [],
        openPicker() { this.$refs.fileInput.click(); },
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