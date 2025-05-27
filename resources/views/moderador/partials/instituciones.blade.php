@php
    use App\Models\Institucion;
    use App\Models\Maestro;
    use App\Models\Especialidad;
    use App\Models\Moderador;
    use Illuminate\Support\Facades\Auth;

    // Get the moderator's id_plantel
    $moderador = Moderador::where('id_user', Auth::id())->first();
    // Fetch instituciones, maestros, and especialidades for the moderator's id_plantel
    $instituciones = $moderador ? Institucion::where('id_plantel', $moderador->id_plantel)->get() : collect();
    $maestros = $moderador ? Maestro::where('id_plantel', $moderador->id_plantel)->with('user', 'institucion')->get() : collect();
    $especialidades = $moderador ? Especialidad::where('id_plantel', $moderador->id_plantel)->with('institucion')->get() : collect();
@endphp

<div class="p-4" x-data="{ 
    activeTab: '{{ $subtab ?? 'instituciones' }}', 
    isFormOpenInstitucion: {{ $errors->any() && old('section') == 'institucion' ? 'true' : 'false' }}, 
    isFormOpenMaestro: {{ $errors->any() && old('section') == 'maestro' ? 'true' : 'false' }}, 
    isFormOpenEspecialidad: {{ $errors->any() && old('section') == 'especialidad' ? 'true' : 'false' }}, 
    editIdInstitucion: null, 
    editIdMaestro: null, 
    editIdEspecialidad: null 
}">
    <!-- Tabs -->
    <div class="mb-4">
        <div class="flex space-x-4 border-b">
            <a 
                href="{{ route('moderador.inicio', ['tab' => 'instituciones']) }}"
                x-on:click="activeTab = 'instituciones'; isFormOpenMaestro = false; isFormOpenEspecialidad = false; editIdMaestro = null; editIdEspecialidad = null" 
                :class="{ 'border-blue-500 text-blue-600': activeTab === 'instituciones', 'text-gray-600': activeTab !== 'instituciones' }" 
                class="px-4 py-2 font-medium border-b-2"
            >
                Instituciones
            </a>
            <a 
                href="{{ route('moderador.inicio', ['tab' => 'maestros']) }}"
                x-on:click="activeTab = 'maestros'; isFormOpenInstitucion = false; isFormOpenEspecialidad = false; editIdInstitucion = null; editIdEspecialidad = null" 
                :class="{ 'border-blue-500 text-blue-600': activeTab === 'maestros', 'text-gray-600': activeTab !== 'maestros' }" 
                class="px-4 py-2 font-medium border-b-2"
            >
                Maestros
            </a>
            <a 
                href="{{ route('moderador.inicio', ['tab' => 'especialidades']) }}"
                x-on:click="activeTab = 'especialidades'; isFormOpenInstitucion = false; isFormOpenMaestro = false; editIdInstitucion = null; editIdMaestro = null" 
                :class="{ 'border-blue-500 text-blue-600': activeTab === 'especialidades', 'text-gray-600': activeTab !== 'especialidades' }" 
                class="px-4 py-2 font-medium border-b-2"
            >
                Especialidades
            </a>
        </div>
    </div>

    <!-- Instituciones Section -->
    <div x-show="activeTab === 'instituciones'" class="flex flex-col lg:flex-row lg:space-x-6 space-y-6 lg:space-y-0">
        <!-- Left Side: Table -->
        <div class="w-full lg:w-1/2 bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Lista de Instituciones</h2>
                <button 
                    x-show="!isFormOpenInstitucion && !editIdInstitucion" 
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
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($instituciones as $institucion)
                                <tr 
                                    x-on:click="editIdInstitucion = editIdInstitucion === {{ $institucion->id }} ? null : {{ $institucion->id }}; isFormOpenInstitucion = false" 
                                    class="cursor-pointer hover:bg-gray-100 transition"
                                    :class="{ 'bg-gray-100': editIdInstitucion === {{ $institucion->id }} }"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $institucion->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $institucion->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Right Side: Form -->
        <div class="w-full lg:w-1/2">
            <!-- Registration Form -->
            <div 
                x-show="isFormOpenInstitucion && !editIdInstitucion" 
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
                    x-show="editIdInstitucion === {{ $institucion->id }}"
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

    <!-- Maestros Section -->
    <div x-show="activeTab === 'maestros'" class="flex flex-col lg:flex-row lg:space-x-6 space-y-6 lg:space-y-0">
        <!-- Left Side: Table -->
        <div class="w-full lg:w-1/2 bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Lista de Maestros</h2>
                <button 
                    x-show="!isFormOpenMaestro && !editIdMaestro" 
                    x-on:click="isFormOpenMaestro = true; editIdMaestro = null" 
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition font-medium"
                >
                    Registrar Nuevo Maestro
                </button>
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
            @if ($maestros->isEmpty())
                <p class="text-gray-600">No hay maestros registrados.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Institución</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($maestros as $maestro)
                                <tr 
                                    x-on:click="editIdMaestro = editIdMaestro === {{ $maestro->id }} ? null : {{ $maestro->id }}; isFormOpenMaestro = false" 
                                    class="cursor-pointer hover:bg-gray-100 transition"
                                    :class="{ 'bg-gray-100': editIdMaestro === {{ $maestro->id }} }"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $maestro->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $maestro->user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $maestro->telefono }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $maestro->institucion->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $maestro->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Right Side: Form -->
        <div class="w-full lg:w-1/2">
            <!-- Registration Form -->
            <div 
                x-show="isFormOpenMaestro && !editIdMaestro" 
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
                                    <option value="{{ $institucion->id }}" {{ old('id_institucion') == $institucion->id ? 'selected' : '' }}>{{ $institucion->name }}</option>
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
                    x-show="editIdMaestro === {{ $maestro->id }}"
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

    <!-- Especialidades Section -->
    <div x-show="activeTab === 'especialidades'" class="flex flex-col lg:flex-row lg:space-x-6 space-y-6 lg:space-y-0">
        <!-- Left Side: Table -->
        <div class="w-full lg:w-1/2 bg-white rounded-lg shadow-md p-6">
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
            @if ($especialidades->isEmpty())
                <p class="text-gray-600">No hay especialidades registradas.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Institución</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($especialidades as $especialidad)
                                <tr 
                                    x-on:click="editIdEspecialidad = editIdEspecialidad === {{ $especialidad->id }} ? null : {{ $especialidad->id }}; isFormOpenEspecialidad = false" 
                                    class="cursor-pointer hover:bg-gray-100 transition"
                                    :class="{ 'bg-gray-100': editIdEspecialidad === {{ $especialidad->id }} }"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $especialidad->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $especialidad->institucion->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $especialidad->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Right Side: Form -->
        <div class="w-full lg:w-1/2">
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
                                    <option value="{{ $institucion->id }}" {{ old('id_institucion') == $institucion->id ? 'selected' : '' }}>{{ $institucion->name }}</option>
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