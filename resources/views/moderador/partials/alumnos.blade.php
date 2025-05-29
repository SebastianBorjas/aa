@php
    $moderador = App\Models\Moderador::where('id_user', Auth::id())->first();
    $alumnos = App\Models\Alumno::where('id_plantel', $moderador->id_plantel)->with(['user', 'empresa', 'institucion', 'maestro', 'especialidad'])->get();
@endphp

<div class="max-w-7xl mx-auto p-4" x-data="{ isFormOpenAlumno: {{ $errors->any() && old('section') == 'alumno' ? 'true' : 'false' }}, editIdAlumno: null, institucionId: '', maestros: [], especialidades: [], lunes: false, martes: false, miercoles: false, jueves: false, viernes: false, sabado: false, domingo: false, async fetchMaestros() { if (!this.institucionId) { this.maestros = []; this.especialidades = []; return; } const response = await fetch('/moderador/maestros-por-institucion/' + this.institucionId); this.maestros = await response.json(); const responseEsp = await fetch('/moderador/especialidades-por-institucion/' + this.institucionId); this.especialidades = await responseEsp.json(); }}">
    <div class="flex flex-col lg:flex-row lg:space-x-6 space-y-6 lg:space-y-0">
        <!-- Left Side: Table -->
        <div class="w-full lg:w-2/3 bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Lista de Alumnos</h2>
                <button 
                    x-show="!isFormOpenAlumno && !editIdAlumno" 
                    x-on:click="isFormOpenAlumno = true; editIdAlumno = null" 
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition font-medium"
                >
                    Registrar Nuevo Alumno
                </button>
            </div>

            <!-- Success/Error Messages -->
            @if (session('success') && session('tab') === 'alumnos')
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any() && old('section') == 'alumno')
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Alumnos Table -->
            @if ($alumnos->isEmpty())
                <p class="text-gray-600">No hay alumnos registrados.</p>
            @else
                <div class="overflow-x-auto max-w-full">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-700 text-white">
                            <tr>
                                <th class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider">Nombre</th>
                                <th class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider">Correo</th>
                                <th class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider">Empresa</th>
                                <th class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider">Institución</th>
                                <th class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider">Especialidad</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-300">
                            @foreach ($alumnos as $alumno)
                                <tr 
                                    x-on:click="editIdAlumno = editIdAlumno === {{ $alumno->id }} ? null : {{ $alumno->id }}; isFormOpenAlumno = false" 
                                    class="cursor-pointer hover:bg-gray-200 transition"
                                    :class="{ 'bg-gray-200': editIdAlumno === {{ $alumno->id }} }"
                                >
                                    <td class="px-2 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $alumno->name }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">{{ $alumno->user->email }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">{{ $alumno->empresa->name ?? 'N/A' }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">{{ $alumno->institucion->name ?? 'N/A' }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">{{ $alumno->especialidad->name ?? 'N/A' }}</td>
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
                x-show="isFormOpenAlumno && !editIdAlumno" 
                class="bg-white rounded-lg shadow-md p-6"
            >
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Registrar Alumno</h2>
                <form method="POST" action="{{ route('moderador.registerAlumno') }}">
                    @csrf
                    <input type="hidden" name="section" value="alumno">
                    <div class="space-y-4">
                        <div>
                            <label for="alumno_correo" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                            <input 
                                type="email" 
                                id="alumno_correo" 
                                name="correo" 
                                value="{{ old('correo') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                placeholder="Ingresa el correo"
                                required
                            >
                        </div>
                        <div>
                            <label for="alumno_contrasena" class="block text-sm font-medium text-gray-700">Contraseña</label>
                            <input 
                                type="password" 
                                id="alumno_contrasena" 
                                name="contrasena"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                placeholder="Ingresa la contraseña"
                                required
                            >
                        </div>
                        <div>
                            <label for="alumno_contrasena_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                            <input 
                                type="password" 
                                id="alumno_contrasena_confirmation" 
                                name="contrasena_confirmation"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                placeholder="Confirma la contraseña"
                                required
                            >
                        </div>
                        <div>
                            <label for="alumno_nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input 
                                type="text" 
                                id="alumno_nombre" 
                                name="nombre" 
                                value="{{ old('nombre') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                placeholder="Ingresa el nombre"
                                required
                            >
                        </div>
                        <div>
                            <label for="alumno_telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input 
                                type="text" 
                                id="alumno_telefono" 
                                name="telefono" 
                                value="{{ old('telefono') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                placeholder="Ingresa el teléfono"
                                required
                            >
                        </div>
                        <div>
                            <label for="alumno_telefono_emergencia" class="block text-sm font-medium text-gray-700">Teléfono de Emergencia</label>
                            <input 
                                type="text" 
                                id="alumno_telefono_emergencia" 
                                name="telefono_emergencia" 
                                value="{{ old('telefono_emergencia') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                placeholder="Ingresa el teléfono de emergencia"
                                required
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Días de Clase</label>
                            <div class="flex gap-2 mt-1">
                                @foreach (['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'] as $day)
                                    <button 
                                        type="button" 
                                        x-on:click="{{ $day }} = !{{ $day }}" 
                                        :class="{{ $day }} ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'" 
                                        class="px-3 py-1 rounded-md"
                                    >
                                        {{ ucfirst($day) }}
                                    </button>
                                    <input type="hidden" name="{{ $day }}" x-bind:value="{{ $day }} ? '1' : '0'">
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label for="alumno_fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                            <input 
                                type="date" 
                                id="alumno_fecha_inicio" 
                                name="fecha_inicio" 
                                value="{{ old('fecha_inicio') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                required
                            >
                        </div>
                        <div>
                            <label for="alumno_fecha_termino" class="block text-sm font-medium text-gray-700">Fecha de Término</label>
                            <input 
                                type="date" 
                                id="alumno_fecha_termino" 
                                name="fecha_termino" 
                                value="{{ old('fecha_termino') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                required
                            >
                        </div>
                        <div>
                            <label for="alumno_id_empresa" class="block text-sm font-medium text-gray-700">Empresa</label>
                            <select 
                                id="alumno_id_empresa" 
                                name="id_empresa" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                required
                            >
                                <option value="" disabled {{ old('id_empresa') ? '' : 'selected' }}>Seleccione una empresa</option>
                                @php
                                    $empresas = App\Models\Empresa::where('id_plantel', $moderador->id_plantel)->get();
                                @endphp
                                @foreach ($empresas as $empresa)
                                    <option value="{{ $empresa->id }}" {{ old('id_empresa') == $empresa->id ? 'selected' : '' }}>{{ $empresa->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="alumno_id_institucion" class="block text-sm font-medium text-gray-700">Institución</label>
                            <select 
                                id="alumno_id_institucion" 
                                name="id_institucion" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                required 
                                x-model="institucionId" 
                                @change="fetchMaestros()"
                            >
                                <option value="" disabled {{ old('id_institucion') ? '' : 'selected' }}>Seleccione una institución</option>
                                @php
                                    $instituciones = App\Models\Institucion::where('id_plantel', $moderador->id_plantel)->get();
                                @endphp
                                @foreach ($instituciones as $institucion)
                                    <option value="{{ $institucion->id }}" {{ old('id_institucion') == $institucion->id ? 'selected' : '' }}>{{ $institucion->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="alumno_id_maestro" class="block text-sm font-medium text-gray-700">Maestro</label>
                            <select 
                                id="alumno_id_maestro" 
                                name="id_maestro" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                required 
                                :disabled="!institucionId"
                            >
                                <option value="" disabled {{ old('id_maestro') ? '' : 'selected' }}>Seleccione un maestro</option>
                                <template x-for="maestro in maestros" :key="maestro.id">
                                    <option :value="maestro.id" x-text="maestro.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label for="alumno_id_especialidad" class="block text-sm font-medium text-gray-700">Especialidad</label>
                            <select 
                                id="alumno_id_especialidad" 
                                name="id_especialidad" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                required 
                                :disabled="!institucionId"
                            >
                                <option value="" disabled {{ old('id_especialidad') ? '' : 'selected' }}>Seleccione una especialidad</option>
                                <template x-for="especialidad in especialidades" :key="especialidad.id">
                                    <option :value="especialidad.id" x-text="especialidad.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button 
                            type="button"
                            x-on:click="isFormOpenAlumno = false" 
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
            @foreach ($alumnos as $alumno)
                <div 
                    x-show="editIdAlumno === {{ $alumno->id }}"
                    class="bg-white rounded-lg shadow-md p-6"
                >
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Editar Alumno</h3>
                    <form method="POST" action="{{ route('moderador.updateAlumno', $alumno) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="section" value="alumno">
                        <div class="space-y-4">
                            <div>
                                <label for="alumno_correo_{{ $alumno->id }}" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                                <input 
                                    type="email" 
                                    id="alumno_correo_{{ $alumno->id }}" 
                                    name="correo" 
                                    value="{{ old('correo', $alumno->user->email) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    required
                                >
                            </div>
                            <div>
                                <label for="alumno_contrasena_{{ $alumno->id }}" class="block text-sm font-medium text-gray-700">Nueva Contraseña (Opcional)</label>
                                <input 
                                    type="password" 
                                    id="alumno_contrasena_{{ $alumno->id }}" 
                                    name="contrasena"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    placeholder="Ingresa nueva contraseña"
                                >
                            </div>
                            <div>
                                <label for="alumno_contrasena_confirmation_{{ $alumno->id }}" class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
                                <input 
                                    type="password" 
                                    id="alumno_contrasena_confirmation_{{ $alumno->id }}" 
                                    name="contrasena_confirmation"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    placeholder="Confirma la contraseña"
                                >
                            </div>
                            <div>
                                <label for="alumno_nombre_{{ $alumno->id }}" class="block text-sm font-medium text-gray-700">Nombre</label>
                                <input 
                                    type="text" 
                                    id="alumno_nombre_{{ $alumno->id }}" 
                                    name="nombre" 
                                    value="{{ old('nombre', $alumno->name) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    required
                                >
                            </div>
                            <div>
                                <label for="alumno_telefono_{{ $alumno->id }}" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                <input 
                                    type="text" 
                                    id="alumno_telefono_{{ $alumno->id }}" 
                                    name="telefono" 
                                    value="{{ old('telefono', $alumno->telefono) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    required
                                >
                            </div>
                            <div>
                                <label for="alumno_telefono_emergencia_{{ $alumno->id }}" class="block text-sm font-medium text-gray-700">Teléfono de Emergencia</label>
                                <input 
                                    type="text" 
                                    id="alumno_telefono_emergencia_{{ $alumno->id }}" 
                                    name="telefono_emergencia" 
                                    value="{{ old('telefono_emergencia', $alumno->telefono_emergencia) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    required
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Días de Clase</label>
                                <div class="flex gap-2 mt-1">
                                    @foreach (['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'] as $day)
                                        <button 
                                            type="button" 
                                            x-on:click="{{ $day }} = !{{ $day }}" 
                                            :class="{{ $day }} ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'" 
                                            class="px-3 py-1 rounded-md"
                                        >
                                            {{ ucfirst($day) }}
                                        </button>
                                        <input type="hidden" name="{{ $day }}" x-bind:value="{{ $day }} ? '1' : '0'" value="{{ old($day, $alumno->$day ? '1' : '0') }}">
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <label for="alumno_fecha_inicio_{{ $alumno->id }}" class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                                <input 
                                    type="date" 
                                    id="alumno_fecha_inicio_{{ $alumno->id }}" 
                                    name="fecha_inicio" 
                                    value="{{ old('fecha_inicio', $alumno->fecha_inicio->format('Y-m-d')) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    required
                                >
                            </div>
                            <div>
                                <label for="alumno_fecha_termino_{{ $alumno->id }}" class="block text-sm font-medium text-gray-700">Fecha de Término</label>
                                <input 
                                    type="date" 
                                    id="alumno_fecha_termino_{{ $alumno->id }}" 
                                    name="fecha_termino" 
                                    value="{{ old('fecha_termino', $alumno->fecha_termino->format('Y-m-d')) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    required
                                >
                            </div>
                            <div>
                                <label for="alumno_id_empresa_{{ $alumno->id }}" class="block text-sm font-medium text-gray-700">Empresa</label>
                                <select 
                                    id="alumno_id_empresa_{{ $alumno->id }}" 
                                    name="id_empresa" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    required
                                >
                                    <option value="" disabled {{ old('id_empresa') ? '' : 'selected' }}>Seleccione una empresa</option>
                                    @foreach ($empresas as $empresa)
                                        <option value="{{ $empresa->id }}" {{ old('id_empresa', $alumno->id_empresa) == $empresa->id ? 'selected' : '' }}>{{ $empresa->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="alumno_id_institucion_{{ $alumno->id }}" class="block text-sm font-medium text-gray-700">Institución</label>
                                <select 
                                    id="alumno_id_institucion_{{ $alumno->id }}" 
                                    name="id_institucion" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    required 
                                    x-model="institucionId" 
                                    @change="fetchMaestros()"
                                >
                                    <option value="" disabled {{ old('id_institucion') ? '' : 'selected' }}>Seleccione una institución</option>
                                    @foreach ($instituciones as $institucion)
                                        <option value="{{ $institucion->id }}" {{ old('id_institucion', $alumno->id_institucion) == $institucion->id ? 'selected' : '' }}>{{ $institucion->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="alumno_id_maestro_{{ $alumno->id }}" class="block text-sm font-medium text-gray-700">Maestro</label>
                                <select 
                                    id="alumno_id_maestro_{{ $alumno->id }}" 
                                    name="id_maestro" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    required 
                                    :disabled="!institucionId"
                                >
                                    <option value="" disabled {{ old('id_maestro') ? '' : 'selected' }}>Seleccione un maestro</option>
                                    <template x-for="maestro in maestros" :key="maestro.id">
                                        <option :value="maestro.id" x-text="maestro.name" :selected="maestro.id == {{ old('id_maestro', $alumno->id_maestro) }}"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label for="alumno_id_especialidad_{{ $alumno->id }}" class="block text-sm font-medium text-gray-700">Especialidad</label>
                                <select 
                                    id="alumno_id_especialidad_{{ $alumno->id }}" 
                                    name="id_especialidad" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    required 
                                    :disabled="!institucionId"
                                >
                                    <option value="" disabled {{ old('id_especialidad') ? '' : 'selected' }}>Seleccione una especialidad</option>
                                    <template x-for="especialidad in especialidades" :key="especialidad.id">
                                        <option :value="especialidad.id" x-text="especialidad.name" :selected="especialidad.id == {{ old('id_especialidad', $alumno->id_especialidad) }}"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button 
                                type="button"
                                x-on:click="editIdAlumno = null" 
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
                                x-on:click="if (confirm('¿Estás seguro de eliminar este alumno?')) { $refs.deleteFormAlumno{{ $alumno->id }}.submit(); }"
                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
                            >
                                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12M10 3h4" />
                                </svg>
                            </button>
                        </div>
                    </form>
                    <form 
                        x-ref="deleteFormAlumno{{ $alumno->id }}"
                        method="POST" 
                        action="{{ route('moderador.deleteAlumno', $alumno) }}" 
                        class="hidden"
                    >
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="section" value="alumno">
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</div>