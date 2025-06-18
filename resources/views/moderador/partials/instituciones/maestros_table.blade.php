@php
    // Set default institution ID (first institution or old input)
    $defaultInstitucionId = old('id_institucion', $instituciones->first()->id ?? null);
    // Filter maestros server-side based on default or selected institution
    $filteredMaestros = $maestros->filter(function ($maestro) use ($defaultInstitucionId) {
        return $maestro->id_institucion == $defaultInstitucionId;
    });
@endphp

<div class="max-w-7xl mx-auto p-4" x-data="{
    selectedInstitucion: {{ $defaultInstitucionId ? json_encode($defaultInstitucionId) : 'null' }},
    isFormOpenMaestro: {{ $errors->any() && old('section') == 'maestro' ? 'true' : 'false' }},
    editIdMaestro: null
}">
    <div class="flex flex-col lg:flex-row lg:space-x-6 space-y-6 lg:space-y-0">
        <!-- Left Side: Table -->
        <div class="w-full lg:w-2/3 bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Lista de Maestros</h2>
                <button 
                    x-show="!isFormOpenMaestro && !editIdMaestro" x-cloak
                    x-on:click="isFormOpenMaestro = true; editIdMaestro = null" 
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition font-medium"
                >
                    Registrar Nuevo Maestro
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
            @if (session('success') && session('tab') === 'maestros')
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any() && old('section') == 'maestro')
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Maestros Table -->
            @if ($filteredMaestros->isEmpty())
                <p class="text-gray-600">No hay maestros registrados para la institución seleccionada.</p>
            @else
                <div class="overflow-x-auto max-w-full">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-300 rounded-lg shadow-sm">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider">Nombre</th>
                                <th class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider">Correo</th>
                                <th class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider">Teléfono</th>
                                <th class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider">Institución</th>
                                <th class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider">Creado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($maestros as $maestro)
                                  <tr
                                      x-show="selectedInstitucion == {{ $maestro->id_institucion }}" x-cloak
                                    x-on:click="editIdMaestro = editIdMaestro === {{ $maestro->id }} ? null : {{ $maestro->id }}; isFormOpenMaestro = false" 
                                    class="cursor-pointer hover:bg-gray-100 transition"
                                    :class="{ 'bg-gray-200': editIdMaestro === {{ $maestro->id }} }"
                                >
                                    <td class="px-2 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $maestro->name }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">{{ $maestro->user->email }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">{{ $maestro->telefono }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">{{ $maestro->institucion->name }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">{{ $maestro->created_at->format('d/m/Y') }}</td>
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
                  x-show="isFormOpenMaestro && !editIdMaestro" x-cloak
                class="bg-white rounded-lg shadow-md p-6"
            >
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Registrar Maestro</h2>
                <form method="POST" action="{{ route('moderador.registerMaestro') }}">
                    @csrf
                    <input type="hidden" name="section" value="maestro">
                    <div class="space-y-4">
                        <div>
                            <label for="nombre_maestro" class="block text-sm font-medium text-gray-700">Nombre del Maestro</label>
                            <input 
                                type="text" 
                                id="nombre_maestro" 
                                name="nombre" 
                                value="{{ old('nombre') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                placeholder="Ingresa el nombre"
                                required
                            >
                        </div>
                        <div>
                            <label for="correo_maestro" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                            <input 
                                type="email" 
                                id="correo_maestro" 
                                name="correo" 
                                value="{{ old('correo') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                placeholder="Ingresa el correo"
                                required
                            >
                        </div>
                        <div>
                            <label for="contrasena_maestro" class="block text-sm font-medium text-gray-700">Contraseña</label>
                            <input 
                                type="password" 
                                id="contrasena_maestro" 
                                name="contrasena"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                placeholder="Ingresa la contraseña"
                                required
                            >
                        </div>
                        <div>
                            <label for="contrasena_confirmation_maestro" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                            <input 
                                type="password" 
                                id="contrasena_confirmation_maestro" 
                                name="contrasena_confirmation"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                placeholder="Confirma la contraseña"
                                required
                            >
                        </div>
                        <div>
                            <label for="telefono_maestro" class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input 
                                type="text" 
                                id="telefono_maestro" 
                                name="telefono" 
                                value="{{ old('telefono') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                placeholder="Ingresa el teléfono"
                                required
                            >
                        </div>
                        <div>
                            <label for="id_institucion_maestro" class="block text-sm font-medium text-gray-700">Institución</label>
                            <select 
                                id="id_institucion_maestro" 
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
                            x-on:click="isFormOpenMaestro = false" 
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
              @foreach ($maestros as $maestro)
                  <div
                      x-show="editIdMaestro === {{ $maestro->id }}" x-cloak
                    class="bg-white rounded-lg shadow-md p-6"
                >
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Editar Maestro</h3>
                    <form method="POST" action="{{ route('moderador.updateMaestro', $maestro) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="section" value="maestro">
                        <div class="space-y-4">
                            <div>
                                <label for="nombre_maestro_{{ $maestro->id }}" class="block text-sm font-medium text-gray-700">Nombre</label>
                                <input 
                                    type="text" 
                                    id="nombre_maestro_{{ $maestro->id }}" 
                                    name="nombre" 
                                    value="{{ old('nombre', $maestro->name) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    required
                                >
                            </div>
                            <div>
                                <label for="correo_maestro_{{ $maestro->id }}" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                                <input 
                                    type="email" 
                                    id="correo_maestro_{{ $maestro->id }}" 
                                    name="correo" 
                                    value="{{ old('correo', $maestro->user->email) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    required
                                >
                            </div>
                            <div>
                                <label for="contrasena_maestro_{{ $maestro->id }}" class="block text-sm font-medium text-gray-700">Nueva Contraseña (Opcional)</label>
                                <input 
                                    type="password" 
                                    id="contrasena_maestro_{{ $maestro->id }}" 
                                    name="contrasena"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    placeholder="Ingresa nueva contraseña"
                                >
                            </div>
                            <div>
                                <label for="contrasena_confirmation_maestro_{{ $maestro->id }}" class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
                                <input 
                                    type="password" 
                                    id="contrasena_confirmation_maestro_{{ $maestro->id }}" 
                                    name="contrasena_confirmation"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    placeholder="Confirma la contraseña"
                                >
                            </div>
                            <div>
                                <label for="telefono_maestro_{{ $maestro->id }}" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                <input 
                                    type="text" 
                                    id="telefono_maestro_{{ $maestro->id }}" 
                                    name="telefono" 
                                    value="{{ old('telefono', $maestro->telefono) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    required
                                >
                            </div>
                            <div>
                                <label for="id_institucion_maestro_{{ $maestro->id }}" class="block text-sm font-medium text-gray-700">Institución</label>
                                <select 
                                    id="id_institucion_maestro_{{ $maestro->id }}" 
                                    name="id_institucion" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    required
                                >
                                    <option value="" disabled {{ old('id_institucion') ? '' : 'selected' }}>Selecciona una institución</option>
                                    @foreach ($instituciones as $institucion)
                                        <option value="{{ $institucion->id }}" {{ old('id_institucion', $maestro->id_institucion) == $institucion->id ? 'selected' : '' }}>{{ $institucion->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button 
                                type="button"
                                x-on:click="editIdMaestro = null" 
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
                                x-on:click="if (confirm('¿Estás seguro de eliminar este maestro?')) { $refs.deleteFormMaestro{{ $maestro->id }}.submit(); }"
                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
                            >
                                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12M10 3h4" />
                                </svg>
                            </button>
                        </div>
                    </form>
                    <form 
                        x-ref="deleteFormMaestro{{ $maestro->id }}"
                        method="POST" 
                        action="{{ route('moderador.deleteMaestro', $maestro) }}" 
                        class="hidden"
                    >
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="section" value="maestro">
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</div>