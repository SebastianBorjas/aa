@extends('layouts.base2')

@push('vite')
  @vite('resources/css/app.css')
  <script src="https://unpkg.com/alpinejs" defer></script>
@endpush

@section('title', 'Tema')

@section('main')
<div x-data="{ sidebarOpen: false, editTema: false }" class="flex flex-col md:flex-row flex-grow relative md:pl-64">
  <button x-show="!sidebarOpen" @click="sidebarOpen = true" class="md:hidden fixed top-4 left-4 z-50 p-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
    </svg>
  </button>

  @include('maestro.partials.sidebar')

  <main class="flex-grow bg-white p-6">
    <div class="mb-4">
      <a href="{{ route('maestro.planes.ver', $tema->id_plan) }}" class="text-blue-600 hover:underline">&larr; Volver al plan</a>
    </div>
    <div class="text-center bg-gray-50 border rounded-lg p-4 max-w-2xl mx-auto">
      <h2 class="text-2xl font-bold text-blue-900">{{ $tema->nombre }}</h2>
      @if($tema->descripcion)
        <p class="mt-2 text-gray-700 whitespace-pre-line max-w-xl mx-auto">{{ $tema->descripcion }}</p>
      @endif
      <button @click="editTema = !editTema" class="mt-2 text-sm text-blue-600 hover:underline">Editar</button>
      <div x-show="editTema" x-cloak class="mt-2">
        <form method="POST" action="{{ route('maestro.temas.save') }}" class="flex flex-col items-center gap-2">
          @csrf
          <input type="hidden" name="id" value="{{ $tema->id }}">
          <input type="hidden" name="id_plan" value="{{ $tema->id_plan }}">
          <input type="text" name="nombre" value="{{ $tema->nombre }}" class="border rounded px-2 py-1" required>
          <textarea name="descripcion" class="border rounded px-2 py-1">{{ $tema->descripcion }}</textarea>
          <button class="px-3 py-1 bg-blue-600 text-white rounded">Guardar</button>
        </form>
      </div>
    </div>

    <div class="mt-6">
      <a href="{{ route('maestro.subtemas.crear', $tema->id) }}" class="px-4 py-2 bg-green-600 text-white rounded">Agregar Subtema</a>
      <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($tema->subtemas as $subtema)
          <a href="{{ route('maestro.subtemas.ver', $subtema->id) }}" class="block bg-gray-50 border border-gray-300 rounded-lg p-4 shadow-sm hover:shadow-md transition-transform transform hover:-translate-y-1 group">
            <div class="flex justify-between items-center">
              <div>
                <div class="font-semibold text-gray-900 group-hover:text-blue-800">{{ $subtema->nombre }}</div>
                @if($subtema->descripcion)
                  <div class="text-xs text-gray-500 mt-1">{{ \Illuminate\Support\Str::limit($subtema->descripcion, 80) }}</div>
                @endif
              </div>
              <svg class="w-5 h-5 text-blue-700 group-hover:text-blue-900 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </div>
          </a>
        @endforeach
      </div>
    </div>
  </main>
</div>
@endsection