@extends('layouts.base2')

@push('vite')
  @vite('resources/css/app.css')
  <script src="https://unpkg.com/alpinejs" defer></script>
@endpush

@section('title', 'Plan')

@section('main')
<div x-data="{ sidebarOpen: false, showNewTema: false, editPlan: false }" class="flex flex-col md:flex-row flex-grow relative md:pl-64">
  <!-- Hamburger Button (Mobile Only) -->
  <button x-show="!sidebarOpen" @click="sidebarOpen = true" class="md:hidden fixed top-4 left-4 z-50 p-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
    </svg>
  </button>

  @include('maestro.partials.sidebar')

  <main class="flex-grow bg-white p-6">
    <div class="mb-4">
      <a href="{{ route('maestro.inicio', ['tab' => 'planes', 'subtab' => 'crear_plan']) }}" class="text-blue-600 hover:underline">&larr; Volver a planes</a>
    </div>
    <div class="text-center">
      <h2 class="text-2xl font-bold text-blue-900 mb-2">{{ $plan->nombre }}</h2>
      <button @click="editPlan = !editPlan" class="text-sm text-blue-600 hover:underline">Editar nombre</button>
      <div x-show="editPlan" x-cloak class="mt-2">
        <form method="POST" action="{{ route('maestro.planes.update', $plan->id) }}" class="flex flex-col items-center gap-2">
          @csrf
          <input type="hidden" name="id_plan" value="{{ $plan->id }}">
          <input type="text" name="nombre" value="{{ $plan->nombre }}" class="border rounded px-2 py-1 text-center" required>
          <button class="px-3 py-1 bg-blue-600 text-white rounded">Guardar</button>
        </form>
      </div>
    </div>

    <div class="mt-6" x-data="{ showNewTema: false }">
      <button @click="showNewTema = !showNewTema" class="px-4 py-2 bg-green-600 text-white rounded">Agregar Tema</button>
      <div x-show="showNewTema" x-cloak class="mt-3 bg-gray-50 p-4 rounded border max-w-md">
        <form method="POST" action="{{ route('maestro.temas.save') }}" class="flex flex-col gap-2">
          @csrf
          <input type="hidden" name="id_plan" value="{{ $plan->id }}">
          <input type="text" name="nombre" class="border rounded px-2 py-1" placeholder="Nombre" required>
          <textarea name="descripcion" class="border rounded px-2 py-1" placeholder="Descripción"></textarea>
          <button class="self-start px-3 py-1 bg-blue-600 text-white rounded">Agregar</button>
        </form>
      </div>

      <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($plan->temas as $tema)
          <a href="{{ route('maestro.temas.ver', $tema->id) }}" class="block bg-gray-50 border border-gray-300 rounded-lg p-4 shadow-sm hover:shadow-md transition-transform transform hover:-translate-y-1 group">
            <div class="flex justify-between items-center">
              <div>
                <div class="font-semibold text-gray-900 group-hover:text-blue-800">{{ $tema->nombre }}</div>
                @if($tema->descripcion)
                  <div class="text-xs text-gray-500 mt-1">{{ \Illuminate\Support\Str::limit($tema->descripcion, 80) }}</div>
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