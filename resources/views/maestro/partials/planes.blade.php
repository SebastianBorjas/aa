@php
    $subtab = request()->query('subtab', 'crear_plan');
@endphp

<div class="p-4 max-w-full mx-auto" 
    x-data="{
        activeTab: '{{ $subtab }}'
    }">
    <!-- Tabs -->
    <div class="mb-4">
        <div class="flex flex-wrap gap-2 border-b border-gray-200 overflow-x-auto">
            <a 
                href="{{ route('maestro.inicio', ['tab' => 'planes', 'subtab' => 'crear_plan']) }}"
                x-on:click="activeTab = 'crear_plan'"
                :class="{ 'border-blue-500 text-blue-600': activeTab === 'crear_plan', 'text-gray-600': activeTab !== 'crear_plan' }"
                class="px-4 py-2 font-medium border-b-2 flex-shrink-0 transition-colors duration-200"
            >Planes</a>
            <a 
                href="{{ route('maestro.inicio', ['tab' => 'planes', 'subtab' => 'asignar_plan']) }}"
                x-on:click="activeTab = 'asignar_plan'"
                :class="{ 'border-blue-500 text-blue-600': activeTab === 'asignar_plan', 'text-gray-600': activeTab !== 'asignar_plan' }"
                class="px-4 py-2 font-medium border-b-2 flex-shrink-0 transition-colors duration-200"
            >Asignar Plan</a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <div x-show="activeTab === 'crear_plan'" x-transition>
            @php
                // Llama al método del controlador para obtener la vista actualizada
                echo app()->call('App\Http\Controllers\MaestroController@planesCrear')->render();
            @endphp

        </div>
        <div x-show="activeTab === 'asignar_plan'" x-transition>
            @include('maestro.partials.planes.asignar_plan')
        </div>
    </div>
</div>
