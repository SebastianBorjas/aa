@extends('layouts.base2')

@push('vite')
  @vite('resources/css/app.css')
  <script src="https://unpkg.com/alpinejs" defer></script>
@endpush

@section('title', 'Nuevo Subtema')

@section('main')
<div x-data="{ sidebarOpen: false }" class="flex flex-col md:flex-row flex-grow relative md:pl-64">
  <button x-show="!sidebarOpen" @click="sidebarOpen = true" class="md:hidden fixed top-4 left-4 z-50 p-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
    </svg>
  </button>

  @include('maestro.partials.sidebar')

  <main class="flex-grow bg-white p-6">
    <div class="mb-4">
      <a href="{{ route('maestro.temas.ver', $tema->id) }}" class="text-blue-600 hover:underline">&larr; Volver a los subtemas</a>
    </div>
    <div class="max-w-xl mx-auto" x-data="fileUploader()">
      <h2 class="text-center text-2xl font-bold text-blue-900 mb-4">Nuevo Subtema</h2>
      <form method="POST" action="{{ route('maestro.subtemas.save') }}" enctype="multipart/form-data" class="space-y-2">
        @csrf
        <input type="hidden" name="id_tema" value="{{ $tema->id }}">
        <input type="text" name="nombre" class="w-full border rounded px-3 py-2" placeholder="Nombre" required>
        <textarea name="descripcion" class="w-full border rounded px-3 py-2" placeholder="Descripción"></textarea>
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
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Crear</button>
      </form>
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