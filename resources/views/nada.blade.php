@extends('layouts.base')

@push('vite')
  @vite('resources/css/app.css')
  <script src="https://unpkg.com/alpinejs" defer></script>
@endpush

@section('title', 'Panel Administrativo')

@section('main')
<div x-data="{ tab: 'dashboard' }" class="flex flex-col sm:flex-row min-h-[calc(100vh-160px)]">
  <!-- Sidebar -->
  <aside class="w-full sm:w-64 bg-[#202c54] text-white p-4 sm:p-6 space-y-4">
    <div class="text-center mb-4">
      <img src="{{ asset('images/lgo3.png') }}" alt="Logo" class="w-10 h-10 mx-auto">
    </div>
    <button @click="tab = 'dashboard'" :class="{ 'bg-[#2b365e]': tab === 'dashboard' }" class="block w-full text-left hover:bg-[#2b365e] px-4 py-2 rounded transition">Dashboard</button>
    <button @click="tab = 'usuarios'" :class="{ 'bg-[#2b365e]': tab === 'usuarios' }" class="block w-full text-left hover:bg-[#2b365e] px-4 py-2 rounded transition">Usuarios</button>
    <button @click="tab = 'reportes'" :class="{ 'bg-[#2b365e]': tab === 'reportes' }" class="block w-full text-left hover:bg-[#2b365e] px-4 py-2 rounded transition">Reportes</button>
  </aside>

  <!-- Contenido principal -->
  <main class="flex-grow bg-white p-6">
    <div x-show="tab === 'dashboard'" x-transition>
      <h2 class="text-2xl font-bold text-gray-800 mb-4">Dashboard</h2>
      <p class="text-gray-600">Bienvenido al panel principal del sistema.</p>
    </div>

    <div x-show="tab === 'usuarios'" x-transition>
      <h2 class="text-2xl font-bold text-gray-800 mb-4">Usuarios</h2>
      <p class="text-gray-600">Gestión de usuarios del sistema.</p>
    </div>

    <div x-show="tab === 'reportes'" x-transition>
      <h2 class="text-2xl font-bold text-gray-800 mb-4">Reportes</h2>
      <p class="text-gray-600">Aquí puedes visualizar reportes y estadísticas.</p>
    </div>
  </main>
</div>
@endsection
