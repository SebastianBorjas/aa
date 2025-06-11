@extends('layouts.base2')

@php
    use Illuminate\Support\Str;
@endphp

@push('vite')
  @vite('resources/css/app.css')
  <script src="https://unpkg.com/alpinejs" defer></script>
@endpush

@section('title', 'Tema')

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
      <a href="{{ route('alumno.inicio', ['tab' => 'tareas']) }}" class="text-blue-600 hover:underline">&larr; Volver al plan</a>
    </div>
    <h2 class="text-2xl font-bold text-blue-900">{{ $tema->nombre }}</h2>
    @if($tema->descripcion)
      <p class="mt-2 text-gray-700 whitespace-pre-line">{{ $tema->descripcion }}</p>
    @endif
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($tema->subtemas as $subtema)
          <a href="{{ route('alumno.subtema', $subtema->id) }}" class="block bg-gray-50 border border-gray-300 rounded-lg p-4 shadow-sm hover:shadow-md transition-transform transform hover:-translate-y-1 group">
            <div class="flex justify-between items-center">
              <div>
                <div class="font-semibold text-gray-900 group-hover:text-blue-800">{{ $subtema->nombre }}</div>
                @if($subtema->descripcion)
                  <div class="text-xs text-gray-500 mt-1">{{ Str::limit($subtema->descripcion, 80) }}</div>
                @endif
              </div>
              <svg class="w-5 h-5 text-blue-700 group-hover:text-blue-900 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </div>
          </a>
        @endforeach
      </div>
  </main>
</div>

<style>
  [x-cloak] { display: none; }
  main { position: relative; z-index: 20; }
</style>
@endsection