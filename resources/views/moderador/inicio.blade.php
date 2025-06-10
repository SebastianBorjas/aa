@extends('layouts.base2')

@push('vite')
  @vite('resources/css/app.css')
  <script src="https://unpkg.com/alpinejs" defer></script>
@endpush

@section('title', 'Panel Moderador')

@section('main')
@php
    $tab = request()->query('tab', 'alumnos');
@endphp
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
      <a href="{{ route('moderador.inicio', ['tab' => 'alumnos']) }}"
         class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium {{ $tab === 'alumnos' ? 'bg-[#2e3a68] text-white' : '' }}">
        Alumnos
      </a>
      <a href="{{ route('moderador.inicio', ['tab' => 'instituciones']) }}"
         class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium {{ in_array($tab, ['instituciones','maestros','especialidades']) ? 'bg-[#2e3a68] text-white' : '' }}">
        Instituciones
      </a>
      <a href="{{ route('moderador.inicio', ['tab' => 'empresas']) }}"
         class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium {{ $tab === 'empresas' ? 'bg-[#2e3a68] text-white' : '' }}">
        Empresas
      </a>
    </nav>

    <form method="POST" action="{{ route('logout') }}" class="pt-4 border-t border-white/20">
      @csrf
      <button type="submit"
              class="w-full mt-2 px-4 py-2 bg-red-600 hover:bg-red-700 rounded text-white font-semibold transition">
        Cerrar sesión
      </button>
    </form>
  </aside>

  <!-- Mobile Sidebar and Overlay -->
  <div x-show="sidebarOpen" x-cloak class="md:hidden">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-40" @click="sidebarOpen = false"></div>
    <!-- Sidebar -->
    <aside x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="transform -translate-x-full"
           x-transition:enter-end="transform translate-x-0"
           x-transition:leave="transition ease-in duration-300"
           x-transition:leave-start="transform translate-x-0"
           x-transition:leave-end="transform -translate-x-full"
           class="fixed left-0 top-0 w-64 bg-[#202c54] text-white p-4 h-full z-50">
      <button @click="sidebarOpen = false" class="mb-4 p-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
      <nav class="flex flex-col gap-2">
        <a href="{{ route('moderador.inicio', ['tab' => 'alumnos']) }}" @click="sidebarOpen = false"
           class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium {{ $tab === 'alumnos' ? 'bg-[#2e3a68] text-white' : '' }}">
          Alumnos
        </a>
        <a href="{{ route('moderador.inicio', ['tab' => 'instituciones']) }}" @click="sidebarOpen = false"
           class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium {{ in_array($tab, ['instituciones','maestros','especialidades']) ? 'bg-[#2e3a68] text-white' : '' }}">
          Instituciones
        </a>
        <a href="{{ route('moderador.inicio', ['tab' => 'empresas']) }}" @click="sidebarOpen = false"
           class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium {{ $tab === 'empresas' ? 'bg-[#2e3a68] text-white' : '' }}">
          Empresas
        </a>
      </nav>

      <form method="POST" action="{{ route('logout') }}" class="pt-4 border-t border-white/20 mt-auto">
        @csrf
        <button type="submit"
                class="w-full mt-2 px-4 py-2 bg-red-600 hover:bg-red-700 rounded text-white font-semibold transition">
          Cerrar sesión
        </button>
      </form>
    </aside>
  </div>

  <!-- Contenido principal -->
  <main class="flex-grow bg-white p-6">
    @if ($tab === 'alumnos')
      @include('moderador.partials.alumnos')
    @elseif (in_array($tab, ['instituciones', 'maestros', 'especialidades']))
      @include('moderador.partials.instituciones', ['subtab' => $tab])
    @elseif ($tab === 'empresas')
      @include('moderador.partials.empresas')
    @endif
  </main>
</div>

<style>
  [x-cloak] { display: none; }
  main {
    position: relative;
    z-index: 20;
  }
</style>
@endsection