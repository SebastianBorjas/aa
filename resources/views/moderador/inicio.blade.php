@extends('layouts.base')

@push('vite')
  @vite('resources/css/app.css')
  <script src="https://unpkg.com/alpinejs" defer></script>
@endpush

@section('title', 'Panel Moderador')

@section('main')
<div x-data="{ tab: 'dashboard', sidebarOpen: false }" class="flex flex-col md:flex-row flex-grow relative">
  <!-- Hamburger Button (Mobile Only) -->
  <button x-show="!sidebarOpen" @click="sidebarOpen = true" class="md:hidden fixed top-4 left-4 z-50 p-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
    </svg>
  </button>

  <!-- Sidebar (Desktop) -->
  <aside class="hidden md:block w-full md:w-64 bg-[#202c54] text-white p-4 space-y-4">
    <nav class="flex flex-col gap-2">
      <button @click="tab = 'dashboard'"
              :class="{ 'bg-[#2e3a68] text-white': tab === 'dashboard' }"
              class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium">
        Dashboard
      </button>
      <button @click="tab = 'usuarios'"
              :class="{ 'bg-[#2e3a68] text-white': tab === 'usuarios' }"
              class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium">
        Usuarios
      </button>
      <button @click="tab = 'reportes'"
              :class="{ 'bg-[#2e3a68] text-white': tab === 'reportes' }"
              class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium">
        Reportes
      </button>
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
        <button @click="tab = 'dashboard'; sidebarOpen = false"
                :class="{ 'bg-[#2e3a68] text-white': tab === 'dashboard' }"
                class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium">
          Dashboard
        </button>
        <button @click="tab = 'usuarios'; sidebarOpen = false"
                :class="{ 'bg-[#2e3a68] text-white': tab === 'usuarios' }"
                class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium">
          Usuarios
        </button>
        <button @click="tab = 'reportes'; sidebarOpen = false"
                :class="{ 'bg-[#2e3a68] text-white': tab === 'reportes' }"
                class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium">
          Reportes
        </button>
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
    <div x-show="tab === 'dashboard'" x-transition>
      <h2 class="text-2xl font-bold text-gray-800 mb-4">Dashboard</h2>
      <p class="text-gray-600">Bienvenido al panel principal del sistema.</p>
    </div>

    <div x-show="tab === 'usuarios'" x-transition>
      @include('moderador.partials.usuarios')
    </div>

    <div x-show="tab === 'reportes'" x-transition>
      @include('moderador.partials.reportes')
    </div>
  </main>
</div>

<style>
  [x-cloak] { display: none; }
  /* Ensure main content stays visible and isn’t pushed out */
  main {
    position: relative;
    z-index: 20; /* Below sidebar (z-50) and overlay (z-40) */
  }
</style>
@endsection