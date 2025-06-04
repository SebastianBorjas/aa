@extends('layouts.base2')

@push('vite')
  @vite('resources/css/app.css')
  <script src="https://unpkg.com/alpinejs" defer></script>
@endpush

@section('title', 'Panel empresa')

@section('main')
<div x-data="{ tab: '{{ request('tab', 'alumnos') }}', sidebarOpen: false }" class="flex flex-col md:flex-row flex-grow relative">
  <!-- Botón Hamburguesa (Mobile) -->
  <button x-show="!sidebarOpen" @click="sidebarOpen = true" class="md:hidden fixed top-4 left-4 z-50 p-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
    </svg>
  </button>

  <!-- Sidebar (Desktop) -->
  <aside class="hidden md:block w-full md:w-64 bg-[#202c54] text-white p-4 space-y-4">
    <nav class="flex flex-col gap-2">
      <button @click="tab = 'alumnos'" :class="{ 'bg-[#2e3a68] text-white': tab === 'alumnos' }" class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium">
        Alumnos
      </button>
      <button @click="tab = 'lista'" :class="{ 'bg-[#2e3a68] text-white': tab === 'lista' }" class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium">
        Lista
      </button>
      <button @click="tab = 'revisar'" :class="{ 'bg-[#2e3a68] text-white': tab === 'revisar' }" class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium">
        Revisar
      </button>
    </nav>
    <form method="POST" action="{{ route('logout') }}" class="pt-4 border-t border-white/20">
      @csrf
      <button type="submit" class="w-full mt-2 px-4 py-2 bg-red-600 hover:bg-red-700 rounded text-white font-semibold transition">
        Cerrar sesión
      </button>
    </form>
  </aside>

  <!-- Sidebar Mobile y Overlay -->
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
           class="fixed left-0 top-0 w-64 bg-[#202c54] text-white p-4 h-full z-50 flex flex-col">
      <button @click="sidebarOpen = false" class="mb-4 p-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
      <nav class="flex flex-col gap-2">
        <button @click="tab = 'alumnos'; sidebarOpen = false" :class="{ 'bg-[#2e3a68] text-white': tab === 'alumnos' }" class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium">
          Alumnos
        </button>
        <button @click="tab = 'lista'; sidebarOpen = false" :class="{ 'bg-[#2e3a68] text-white': tab === 'lista' }" class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium">
          Lista
        </button>
        <button @click="tab = 'revisar'; sidebarOpen = false" :class="{ 'bg-[#2e3a68] text-white': tab === 'revisar' }" class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium">
          Revisar
        </button>
      </nav>
      <form method="POST" action="{{ route('logout') }}" class="pt-4 border-t border-white/20 mt-auto">
        @csrf
        <button type="submit" class="w-full mt-2 px-4 py-2 bg-red-600 hover:bg-red-700 rounded text-white font-semibold transition">
          Cerrar sesión
        </button>
      </form>
    </aside>
  </div>

  <!-- Contenido principal -->
  <main class="flex-grow bg-white p-6">
    <template x-if="tab === 'alumnos'">
      <div x-cloak>
        @include('empresa.partials.alumnos')
      </div>
    </template>
    <template x-if="tab === 'lista'">
      <div x-cloak>
        @include('empresa.partials.lista')
      </div>
    </template>
    <template x-if="tab === 'revisar'">
      <div x-cloak>
        @include('empresa.partials.revisar')
      </div>
    </template>
  </main>
</div>

<style>
  [x-cloak] { display: none; }
  main { position: relative; z-index: 20; }
</style>
@endsection
