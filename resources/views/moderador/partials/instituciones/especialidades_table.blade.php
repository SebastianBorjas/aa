@php
    // Set default institution ID (first institution or old input)
    $defaultInstitucionId = old('id_institucion', $instituciones->first()->id ?? null);
    // Filter especialidades server-side based on default or selected institution
    $filteredEspecialidades = $especialidades->filter(function ($especialidad) use ($defaultInstitucionId) {
        return $especialidad->id_institucion == $defaultInstitucionId;
    });
@endphp

<div class="max-w-7xl mx-auto p-4" x-data="{
    selectedInstitucion: {{ $defaultInstitucionId ? json_encode($defaultInstitucionId) : 'null' }},
    isFormOpenEspecialidad: {{ $errors->any() && old('section') == 'especialidad' ? 'true' : 'false' }},
    editIdEspecialidad: null
}">
    <div class="flex flex-col lg:flex-row lg:space-x-6 space-y-6 lg:space-y-0">
        <!-- Left Side: Table -->
        <div class="w-full lg:w-2/3 bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Lista de Especialidades</h2>
                <button 
                    x-show="!isFormOpenEspecialidad && !editIdEspecialidad" 
                    x-on:click="isFormOpenEspecialidad = true; editIdEspecialidad = null" 
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition font-medium"
                >
                    Registrar Nueva Especialidad
                </button>
            </div>

            <!-- Institution Filter -->
            <div class="mb-4">
                <label for="institucion_filter" class="block text-sm font-medium text-gray-700">Filtrar por Institución</label>
                <select 
                    id="institucion_filter" 
                    x-model="selectedInstitucion"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                >
                    @foreach ($instituciones as $institucion)
                        <option value="{{ $institucion->id }}" {{ $defaultInstitucionId == $institucion->id ? 'selected' : '' }}>{{ $institucion->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Success/Error Messages -->
            @if (session('success') && session('tab') === 'especialidades')
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any() && old('section') == 'especialidad')
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Especialidades Table -->
            @if ($filteredEspecialidades->isEmpty())
                <p class="text-gray-600">No hay especialidades registradas para la institución seleccionada.</p>
            @else
                <div class="overflow-x-auto max-w-full">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-700 text-white">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Nombre</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Institución</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Creado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-300">
                            @foreach ($especialidades as $especialidad)
                                <tr 
                                    x-show="selectedInstitucion == {{ $especialidad->id_institucion }}"
                                    x-on:click="editIdEspecialidad = editIdEspecialidad === {{ $especialidad->id }} ? null : {{ $especialidad->id }}; isFormOpenEspecialidad = false" 
                                    class="cursor-pointer hover:bg-gray-200 transition"
                                    :class="{ 'bg-gray-200': editIdEspecialidad === {{ $especialidad->id }} }"
                                >
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $especialidad->name }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $especialidad->institucion->name }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $especialidad->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Right Side: Form -->
        <div class="w-full lg:w-1/3">
            <!-- Registration Form -->
            <div 
                x-show="isFormOpenEspecialidad && !editIdEspecialidad" 
                class="bg-white rounded-lg shadow-md p-6"
            >
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Registrar Especialidad</h2>
                <form method="POST" action="{{ route('moderador.registerEspecialidad') }}">
                    @csrf
                    <input type="hidden" name="section" value="especialidad">
                    <div class="space-y-4">
                        <div>
                            <label for="nombre_especialidad" class="block text-sm font-medium text-gray-700">Nombre de la Especialidad</label>
                            <input 
                                type="text" 
                                id="nombre_especialidad" 
                                name="nombre" 
                                value="{{ old('nombre') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                placeholder="Ingresa el nombre"
                                required
                            >
                        </div>
                        <div>
                            <label for="id_institucion_especialidad" class="block text-sm font-medium text-gray-700">Institución</label>
                            <select 
                                id="id_institucion_especialidad" 
                                name="id_institucion" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                required
                            >
                                <option value="" disabled {{ old('id_institucion') ? '' : 'selected' }}>Selecciona una institución</option>
                                @foreach ($instituciones as $institucion)
                                    <option value="{{ $institucion->id }}" {{ old('id_institucion', $defaultInstitucionId) == $institucion->id ? 'selected' : '' }}>{{ $institucion->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button 
                            type="button"
                            x-on:click="isFormOpenEspecialidad = false" 
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                        >
                            Registrar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Edit Form -->
            @foreach ($especialidades as $especialidad)
                <div 
                    x-show="editIdEspecialidad === {{ $especialidad->id }}"
                    class="bg-white rounded-lg shadow-md p-6"
                >
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Editar Especialidad</h3>
                    <form method="POST" action="{{ route('moderador.updateEspecialidad', $especialidad) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="section" value="especialidad">
                        <div class="space-y-4">
                            <div>
                                <label for="nombre_especialidad_{{ $especialidad->id }}" class="block text-sm font-medium text-gray-700">Nombre</label>
                                <input 
                                    type="text" 
                                    id="nombre_especialidad_{{ $especialidad->id }}" 
                                    name="nombre" 
                                    value="{{ old('nombre', $especialidad->name) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    required
                                >
                            </div>
                            <div>
                                <label for="id_institucion_especialidad_{{ $especialidad->id }}" class="block text-sm font-medium text-gray-700">Institución</label>
                                <select 
                                    id="id_institucion_especialidad_{{ $especialidad->id }}" 
                                    name="id_institucion" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    required
                                >
                                    <option value="" disabled {{ old('id_institucion') ? '' : 'selected' }}>Selecciona una institución</option>
                                    @foreach ($instituciones as $institucion)
                                        <option value="{{ $institucion->id }}" {{ old('id_institucion', $especialidad->id_institucion) == $institucion->id ? 'selected' : '' }}>{{ $institucion->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button 
                                type="button"
                                x-on:click="editIdEspecialidad = null" 
                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition"
                            >
                                Cancelar
                            </button>
                            <button 
                                type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                            >
                                Actualizar
                            </button>
                            <button 
                                type="button"
                                x-on:click="if (confirm('¿Estás seguro de eliminar esta especialidad?')) { $refs.deleteFormEspecialidad{{ $especialidad->id }}.submit(); }"
                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
                            >
                                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12M10 3h4" />
                                </svg>
                            </button>
                        </div>
                    </form>
                    <form 
                        x-ref="deleteFormEspecialidad{{ $especialidad->id }}"
                        method="POST" 
                        action="{{ route('moderador.deleteEspecialidad', $especialidad) }}" 
                        class="hidden"
                    >
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="section" value="especialidad">
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</div>