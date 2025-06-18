<div class="max-w-7xl mx-auto p-4" x-data="{ isFormOpenInstitucion: {{ $errors->any() && old('section') == 'institucion' ? 'true' : 'false' }}, editIdInstitucion: null }">
    <div class="flex flex-col lg:flex-row lg:space-x-6 space-y-6 lg:space-y-0">
        <!-- Left Side: Table -->
        <div class="w-full lg:w-2/3 bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Lista de Instituciones</h2>
        <button
                    x-show="!isFormOpenInstitucion && !editIdInstitucion" x-cloak
                    x-on:click="isFormOpenInstitucion = true; editIdInstitucion = null" 
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition font-medium"
                >
                    Registrar Nueva Institución
                </button>
            </div>

            <!-- Success/Error Messages -->
            @if (session('success') && session('tab') === 'instituciones')
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any() && old('section') == 'institucion')
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Instituciones Table -->
            @if ($instituciones->isEmpty())
                <p class="text-gray-600">No hay instituciones registradas.</p>
            @else
                <div class="overflow-x-auto max-w-full">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-300 rounded-lg shadow-sm">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider">Nombre</th>
                                <th class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider">Creado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($instituciones as $institucion)
                                <tr 
                                    x-on:click="editIdInstitucion = editIdInstitucion === {{ $institucion->id }} ? null : {{ $institucion->id }}; isFormOpenInstitucion = false" 
                                    class="cursor-pointer hover:bg-gray-100 transition"
                                    :class="{ 'bg-gray-200': editIdInstitucion === {{ $institucion->id }} }"
                                >
                                    <td class="px-2 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $institucion->name }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">{{ $institucion->created_at->format('d/m/Y') }}</td>
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
                  x-show="isFormOpenInstitucion && !editIdInstitucion" x-cloak
                class="bg-white rounded-lg shadow-md p-6"
            >
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Registrar Institución</h2>
                <form method="POST" action="{{ route('moderador.registerInstitucion') }}">
                    @csrf
                    <input type="hidden" name="section" value="institucion">
                    <div class="space-y-4">
                        <div>
                            <label for="nombre_institucion" class="block text-sm font-medium text-gray-700">Nombre de la Institución</label>
                            <input 
                                type="text" 
                                id="nombre_institucion" 
                                name="nombre" 
                                value="{{ old('nombre') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                placeholder="Ingresa el nombre"
                                required
                            >
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button 
                            type="button"
                            x-on:click="isFormOpenInstitucion = false" 
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
              @foreach ($instituciones as $institucion)
                  <div
                      x-show="editIdInstitucion === {{ $institucion->id }}" x-cloak
                    class="bg-white rounded-lg shadow-md p-6"
                >
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Editar Institución</h3>
                    <form method="POST" action="{{ route('moderador.updateInstitucion', $institucion) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="section" value="institucion">
                        <div class="space-y-4">
                            <div>
                                <label for="nombre_institucion_{{ $institucion->id }}" class="block text-sm font-medium text-gray-700">Nombre</label>
                                <input 
                                    type="text" 
                                    id="nombre_institucion_{{ $institucion->id }}" 
                                    name="nombre" 
                                    value="{{ old('nombre', $institucion->name) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    required
                                >
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button 
                                type="button"
                                x-on:click="editIdInstitucion = null" 
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
                                x-on:click="if (confirm('¿Estás seguro de eliminar esta institución?')) { $refs.deleteFormInstitucion{{ $institucion->id }}.submit(); }"
                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
                            >
                                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12M10 3h4" />
                                </svg>
                            </button>
                        </div>
                    </form>
                    <form 
                        x-ref="deleteFormInstitucion{{ $institucion->id }}"
                        method="POST" 
                        action="{{ route('moderador.deleteInstitucion', $institucion) }}" 
                        class="hidden"
                    >
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="section" value="institucion">
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</div>