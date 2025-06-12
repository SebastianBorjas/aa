<!-- Sidebar (Desktop) -->
<aside class="hidden md:block md:fixed md:left-0 md:top-16 md:h-[calc(100vh_-_4rem)] w-64 bg-[#202c54] text-white p-4 space-y-4 overflow-y-auto">
    <nav class="flex flex-col gap-2">
      <a href="{{ route('maestro.inicio', ['tab' => 'alumnos']) }}"
         :class="{ 'bg-[#2e3a68] text-white': tab === 'alumnos' }"
         class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium">
        Alumnos
      </a>
      <a href="{{ route('maestro.inicio', ['tab' => 'planes']) }}"
         :class="{ 'bg-[#2e3a68] text-white': tab === 'planes' }"
         class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium">
        Planes
      </a>
      <a href="{{ route('maestro.inicio', ['tab' => 'revision']) }}"
         :class="{ 'bg-[#2e3a68] text-white': tab === 'revision' }"
         class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium">
        Revisión
        @if(($revisionPendientes ?? 0) > 0)
          <span class="ml-2 inline-block w-2 h-2 bg-red-500 rounded-full"></span>
        @endif
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
    <div class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40" @click="sidebarOpen = false"></div>
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
        <a href="{{ route('maestro.inicio', ['tab' => 'alumnos']) }}"
           @click="tab = 'alumnos'; sidebarOpen = false"
           :class="{ 'bg-[#2e3a68] text-white': tab === 'alumnos' }"
           class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium">
          Alumnos
        </a>
        <a href="{{ route('maestro.inicio', ['tab' => 'planes']) }}"
           @click="tab = 'planes'; sidebarOpen = false"
           :class="{ 'bg-[#2e3a68] text-white': tab === 'planes' }"
           class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium">
          Planes
        </a>
        <a href="{{ route('maestro.inicio', ['tab' => 'revision']) }}"
           @click="tab = 'revision'; sidebarOpen = false"
           :class="{ 'bg-[#2e3a68] text-white': tab === 'revision' }"
           class="px-4 py-2 rounded hover:bg-[#2e3a68] transition text-left font-medium">
          Revisión
          @if(($revisionPendientes ?? 0) > 0)
            <span class="ml-2 inline-block w-2 h-2 bg-red-500 rounded-full"></span>
          @endif
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