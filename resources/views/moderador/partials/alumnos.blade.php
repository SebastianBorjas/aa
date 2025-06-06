@php
    $moderador = App\Models\Moderador::where('id_user', Auth::id())->first();
    $alumnos = App\Models\Alumno::where('id_plantel', $moderador->id_plantel)
        ->with(['user', 'empresa', 'institucion', 'maestro', 'especialidad', 'listas'])
        ->get();
@endphp

<div class="max-w-7xl mx-auto p-4" x-data="{
    isFormOpenAlumno: {{ $errors->any() && old('section') == 'alumno' ? 'true' : 'false' }},
    editIdAlumno: null,
    isEditModeAlumno: false,
    institucionId: '{{ old('id_institucion') }}',
    maestros: [],
    especialidades: [],
    lunes: {{ old('lunes', 0) ? 'true' : 'false' }},
    martes: {{ old('martes', 0) ? 'true' : 'false' }},
    miercoles: {{ old('miercoles', 0) ? 'true' : 'false' }},
    jueves: {{ old('jueves', 0) ? 'true' : 'false' }},
    viernes: {{ old('viernes', 0) ? 'true' : 'false' }},
    sabado: {{ old('sabado', 0) ? 'true' : 'false' }},
    domingo: {{ old('domingo', 0) ? 'true' : 'false' }},
    async fetchMaestros() {
        if (!this.institucionId) {
            this.maestros = [];
            this.especialidades = [];
            return;
        }
        const response = await fetch('/moderador/maestros-por-institucion/' + this.institucionId);
        this.maestros = await response.json();
        const responseEsp = await fetch('/moderador/especialidades-por-institucion/' + this.institucionId);
        this.especialidades = await responseEsp.json();
    }
}">
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
                                    x-on:click="editIdAlumno = editIdAlumno === {{ $alumno->id }} ? null : {{ $alumno->id }}; isFormOpenAlumno = false; isEditModeAlumno = false"
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
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" 
                                placeholder="Confirma la contraseña"
                                required
                            >
                        </div>
                        <div>
                            <label for="alumno_name" class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input 
                                type="text" 
                                id="alumno_name" 
                                name="nombre" 
                                value="{{ old('nombre') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" 
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
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" 
                                placeholder="Teléfono"
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
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" 
                                placeholder="Teléfono de emergencia"
                                required
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Días de Clase</label>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @foreach (['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'] as $en)
                                    <button 
                                        type="button" 
                                        x-on:click="{{ $en }} = !{{ $en }}" 
                                        :class="{{ $en }} ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'" 
                                        class="px-3 py-1 rounded-md"
                                    >
                                        {{ ucfirst($en) }}
                                    </button>
                                    <input type="hidden" name="{{ $en }}" x-bind:value="{{ $en }} ? 1 : 0">
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
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" 
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
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" 
                                required
                            >
                        </div>
                        <div>
                            <label for="alumno_id_empresa" class="block text-sm font-medium text-gray-700">Empresa</label>
                            <select 
                                id="alumno_id_empresa" 
                                name="id_empresa" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" 
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
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" 
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
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" 
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
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" 
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

            <!-- Detail and Edit -->
            @foreach ($alumnos as $alumno)
                <!-- View Details -->
                @php
                    $attendanceData = $alumno->listas->map(fn($l) => [
                        'fecha' => $l->fecha->format('Y-m-d'),
                        'estado' => $l->estado,
                    ]);
                @endphp
                <div
                    x-show="editIdAlumno === {{ $alumno->id }} && !isEditModeAlumno"
                    class="bg-white rounded-lg shadow-md p-6"
                >
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Alumno</h3>
                    <div class="space-y-2">
                        <p><strong>Correo:</strong> {{ $alumno->user->email }}</p>
                        <p><strong>Nombre:</strong> {{ $alumno->name }}</p>
                        <p><strong>Teléfono:</strong> {{ $alumno->telefono }}</p>
                        <p><strong>Teléfono Emergencia:</strong> {{ $alumno->telefono_emergencia }}</p>
                        <p><strong>Empresa:</strong> {{ $alumno->empresa->name ?? 'N/A' }}</p>
                        <p><strong>Institución:</strong> {{ $alumno->institucion->name ?? 'N/A' }}</p>
                        <p><strong>Especialidad:</strong> {{ $alumno->especialidad->name ?? 'N/A' }}</p>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Static Calendar -->
                        <div class="bg-gray-100 p-4 rounded" x-data='calendar(@json($attendanceData))' x-init="init()">
                            <div class="flex items-center justify-between mb-2">
                                <button type="button" @click="prevMonth" class="text-sm px-2 py-1 bg-gray-200 rounded">&#8249;</button>
                                <div class="text-sm font-semibold" x-text="monthNames[currentMonth] + ' ' + currentYear"></div>
                                <button type="button" @click="nextMonth" class="text-sm px-2 py-1 bg-gray-200 rounded">&#8250;</button>
                            </div>
                            <div class="grid grid-cols-7 text-xs font-semibold text-center">
                                <template x-for="day in dayNames" :key="day">
                                    <div class="py-1" x-text="day"></div>
                                </template>
                            </div>
                            <div class="grid grid-cols-7 text-center text-sm">
                                <template x-for="blank in blanks" :key="'b' + blank">
                                    <div></div>
                                </template>
                                <template x-for="date in dates" :key="date.day">
                                    <div class="py-1 flex justify-center">
                                        <div class="w-6 h-6 flex items-center justify-center rounded-full"
                                             :class="{
                                                 'bg-green-600 text-white': date.estado === 'asistencia',
                                                 'bg-red-600 text-white': date.estado === 'falta',
                                                 'bg-blue-600 text-white': date.estado === 'justificado'
                                             }">
                                            <span x-text="date.day"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Static Aesthetic Chart -->
                        <div class="bg-gray-100 p-4 rounded">
                            <p class="text-sm font-medium text-gray-600 mb-2">Gráfica</p>
                            <canvas id="fakeChart{{ $alumno->id }}" style="height: 150px;"></canvas>
                            <style>
                                #fakeChart{{ $alumno->id }} {
                                    background: linear-gradient(to right, #4ade80 30%, #22c55e 60%, #15803d 100%);
                                    border-radius: 8px;
                                    width: 100%;
                                }
                            </style>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" x-on:click="isEditModeAlumno = true" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Editar</button>
                        <button type="button" x-on:click="editIdAlumno = null" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">Cerrar</button>
                    </div>
                </div>

                <!-- Edit Form -->
                <div
                    x-show="editIdAlumno === {{ $alumno->id }} && isEditModeAlumno"
                    class="bg-white rounded-lg shadow-md p-6"
                    x-data="{
                        lunes: {{ $alumno->lunes ? 'true' : 'false' }},
                        martes: {{ $alumno->martes ? 'true' : 'false' }},
                        miercoles: {{ $alumno->miercoles ? 'true' : 'false' }},
                        jueves: {{ $alumno->jueves ? 'true' : 'false' }},
                        viernes: {{ $alumno->viernes ? 'true' : 'false' }},
                        sabado: {{ $alumno->sabado ? 'true' : 'false' }},
                        domingo: {{ $alumno->domingo ? 'true' : 'false' }}
                    }"
                >
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Editar Alumno</h3>
                    <form method="POST" action="{{ route('moderador.updateAlumno', $alumno) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="section" value="alumno">
                        <input type="hidden" name="id_institucion" value="{{ $alumno->id_institucion }}">
                        <input type="hidden" name="id_maestro" value="{{ $alumno->id_maestro }}">
                        <input type="hidden" name="id_especialidad" value="{{ $alumno->id_especialidad }}">
                        <div class="space-y-4">
                            <div>
                                <label for="alumno_correo_{{ $alumno->id }}" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                                <input 
                                    type="email" 
                                    id="alumno_correo_{{ $alumno->id }}" 
                                    name="correo" 
                                    value="{{ old('correo', $alumno->user->email) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" 
                                    required
                                >
                            </div>
                            <div>
                                <label for="alumno_contrasena_{{ $alumno->id }}" class="block text-sm font-medium text-gray-700">Nueva Contraseña (Opcional)</label>
                                <input 
                                    type="password" 
                                    id="alumno_contrasena_{{ $alumno->id }}" 
                                    name="contrasena"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" 
                                    placeholder="Ingresa nueva contraseña"
                                >
                            </div>
                            <div>
                                <label for="alumno_contrasena_confirmation_{{ $alumno->id }}" class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
                                <input 
                                    type="password" 
                                    id="alumno_contrasena_confirmation_{{ $alumno->id }}" 
                                    name="contrasena_confirmation"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" 
                                    placeholder="Confirma la contraseña"
                                >
                            </div>
                            <div>
                                <label for="alumno_name_{{ $alumno->id }}" class="block text-sm font-medium text-gray-700">Nombre</label>
                                <input 
                                    type="text" 
                                    id="alumno_name_{{ $alumno->id }}" 
                                    name="nombre" 
                                    value="{{ old('nombre', $alumno->name) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" 
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
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" 
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
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" 
                                    required
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Días de Clase</label>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach (['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'] as $en)
                                        <button 
                                            type="button" 
                                            x-on:click="{{ $en }} = !{{ $en }}" 
                                            :class="{{ $en }} ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'" 
                                            class="px-3 py-1 rounded-md"
                                        >
                                            {{ ucfirst($en) }}
                                        </button>
                                        <input type="hidden" name="{{ $en }}" x-bind:value="{{ $en }} ? 1 : 0">
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
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" 
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
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" 
                                    required
                                >
                            </div>
                            <div>
                                <label for="alumno_id_empresa_{{ $alumno->id }}" class="block text-sm font-medium text-gray-700">Empresa</label>
                                <select 
                                    id="alumno_id_empresa_{{ $alumno->id }}" 
                                    name="id_empresa" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" 
                                    required
                                >
                                    <option value="" disabled {{ old('id_empresa') ? '' : 'selected' }}>Seleccione una empresa</option>
                                    @foreach ($empresas as $empresa)
                                        <option value="{{ $empresa->id }}" {{ old('id_empresa', $alumno->id_empresa) == $empresa->id ? 'selected' : '' }}>{{ $empresa->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button 
                                type="button"
                                x-on:click="editIdAlumno = null; isEditModeAlumno = false"
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
    <script>
        function calendar(records = []) {
            return {
                currentDate: new Date(),
                dayNames: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
                monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
                blanks: [],
                dates: [],
                records: records,
                get currentMonth() { return this.currentDate.getMonth(); },
                get currentYear() { return this.currentDate.getFullYear(); },
                init() { this.update(); },
                prevMonth() {
                    this.currentDate = new Date(this.currentYear, this.currentMonth - 1, 1);
                    this.update();
                },
                nextMonth() {
                    this.currentDate = new Date(this.currentYear, this.currentMonth + 1, 1);
                    this.update();
                },
                update() {
                    const first = new Date(this.currentYear, this.currentMonth, 1).getDay();
                    const total = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                    this.blanks = Array.from({ length: first }, (_, i) => i);
                    this.dates = Array.from({ length: total }, (_, i) => {
                        const day = i + 1;
                        const dateStr = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
                        const rec = this.records.find(r => r.fecha === dateStr);
                        return { day: day, estado: rec ? rec.estado : null };
                    });
                }
            };
        }
    </script>
</div>