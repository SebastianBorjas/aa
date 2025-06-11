@extends('layouts.base2')

@push('vite')
  @vite('resources/css/app.css')
  <script src="https://unpkg.com/alpinejs" defer></script>
@endpush

@section('title', 'Subtema')

@section('main')
<div x-data="{ sidebarOpen: false, editSubtema: false }" class="flex flex-col md:flex-row flex-grow relative md:pl-64">
  <button x-show="!sidebarOpen" @click="sidebarOpen = true" class="md:hidden fixed top-4 left-4 z-50 p-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
    </svg>
  </button>

  @include('maestro.partials.sidebar')

  <main class="flex-grow bg-white p-6">
    <div class="mb-4">
      <a href="{{ route('maestro.temas.ver', $subtema->id_tema) }}" class="text-blue-600 hover:underline">&larr; Volver a los subtemas</a>
    </div>
    <div class="text-center bg-gray-50 border rounded-lg p-4 max-w-2xl mx-auto">
      <h2 class="text-2xl font-bold text-blue-900">{{ $subtema->nombre }}</h2>
      @if($subtema->descripcion)
        <p class="mt-2 text-gray-700 whitespace-pre-line max-w-xl mx-auto">{{ $subtema->descripcion }}</p>
      @endif
      <button @click="editSubtema = !editSubtema" class="mt-2 text-sm text-blue-600 hover:underline">Editar</button>
    </div>

    <div x-show="editSubtema" x-cloak class="mt-4 max-w-md mx-auto">
      <form method="POST" action="{{ route('maestro.subtemas.save') }}" class="flex flex-col gap-2">
        @csrf
        <input type="hidden" name="id" value="{{ $subtema->id }}">
        <input type="hidden" name="id_tema" value="{{ $subtema->id_tema }}">
        <input type="text" name="nombre" value="{{ $subtema->nombre }}" class="border rounded px-2 py-1" required>
        <textarea name="descripcion" class="border rounded px-2 py-1">{{ $subtema->descripcion }}</textarea>
        <button class="px-3 py-1 bg-blue-600 text-white rounded self-start">Guardar</button>
      </form>
    </div>

    <div class="mt-6 max-w-xl mx-auto">
      <h3 class="font-semibold text-blue-700 mb-2">Archivos</h3>
      @if($subtema->rutas)
        <ul class="flex flex-wrap gap-2">
          @foreach($subtema->rutas as $i => $ruta)
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
              <form method="POST" action="{{ route('maestro.subtemas.deletefile', $subtema->id) }}" onsubmit="return confirm('¿Eliminar archivo?')" class="absolute bottom-1 left-1">
                @csrf
                <input type="hidden" name="file_index" value="{{ $i }}">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white p-1 rounded-full">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m-1 0h8" />
                  </svg>
                </button>
              </form>
            </li>
          @endforeach
        </ul>
      @else
        <p class="text-gray-500">No hay archivos</p>
      @endif
      <div class="mt-2" x-data="fileUploader()">
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
          <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">Subir</button>
        </form>
      </div>
    </div>
  </main>
</div>
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