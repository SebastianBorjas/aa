@php
    $moderador = App\Models\Moderador::where('id_user', Auth::id())->first();
    $alumnos = App\Models\Alumno::where('id_plantel', $moderador->id_plantel)
        ->with([
            'user',
            'empresa',
            'institucion',
            'maestro',
            'especialidad',
            'listas',
            'plan.temas.subtemas',
            'entregas',
        ])
        ->get();

    $empresasFiltro = App\Models\Empresa::where('id_plantel', $moderador->id_plantel)->get();
    $institucionesFiltro = App\Models\Institucion::where('id_plantel', $moderador->id_plantel)->get();
@endphp

<div class="max-w-7xl mx-auto p-4" x-data="{
    isFormOpenAlumno: {{ $errors->any() && old('section') == 'alumno' ? 'true' : 'false' }},
    editIdAlumno: null,
    isEditModeAlumno: false,
    showPlanId: null,
    institucionId: '{{ old('id_institucion') }}',
    maestros: [],
    especialidades: [],
    selectedEmpresa: '',
    selectedInstitucion: '',
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
            @if (session('error') && session('tab') === 'alumnos')
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    {{ session('error') }}
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

            <!-- Filters -->
            <div class="mb-4 flex flex-col md:flex-row md:space-x-4">
                <div class="flex-1">
                    <label for="empresa_filter" class="block text-sm font-medium text-gray-700">Filtrar por Empresa</label>
                    <select id="empresa_filter" x-model="selectedEmpresa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="">Todas</option>
                        @foreach ($empresasFiltro as $empresa)
                            <option value="{{ $empresa->id }}">{{ $empresa->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 mt-4 md:mt-0">
                    <label for="institucion_filter" class="block text-sm font-medium text-gray-700">Filtrar por Institución</label>
                    <select id="institucion_filter" x-model="selectedInstitucion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="">Todas</option>
                        @foreach ($institucionesFiltro as $institucion)
                            <option value="{{ $institucion->id }}">{{ $institucion->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Alumnos Table -->
            @if ($alumnos->isEmpty())
                <p class="text-gray-600">No hay alumnos registrados.</p>
            @else
                <div class="overflow-x-auto max-w-full">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-300 rounded-lg shadow-sm">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Nombre</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Correo</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Empresa</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Institución</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Especialidad</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($alumnos as $alumno)
                                <tr
                                   x-on:click="editIdAlumno = editIdAlumno === {{ $alumno->id }} ? null : {{ $alumno->id }}; isFormOpenAlumno = false; isEditModeAlumno = false; showPlanId = null"
                                    x-show="(selectedEmpresa === '' || selectedEmpresa == {{ $alumno->id_empresa }}) &&
                                             (selectedInstitucion === '' || selectedInstitucion == {{ $alumno->id_institucion }})"
                                    class="cursor-pointer hover:bg-gray-100 transition"
                                    :class="{ 'bg-gray-200': editIdAlumno === {{ $alumno->id }} }"
                                >
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $alumno->name }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $alumno->user->email }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $alumno->empresa->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $alumno->institucion->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $alumno->especialidad->name ?? 'N/A' }}</td>
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
                    $diasActivos = [];
                    if ($alumno->domingo) $diasActivos[] = 0;
                    if ($alumno->lunes) $diasActivos[] = 1;
                    if ($alumno->martes) $diasActivos[] = 2;
                    if ($alumno->miercoles) $diasActivos[] = 3;
                    if ($alumno->jueves) $diasActivos[] = 4;
                    if ($alumno->viernes) $diasActivos[] = 5;
                    if ($alumno->sabado) $diasActivos[] = 6;
                @endphp
                <div
                    x-show="editIdAlumno === {{ $alumno->id }} && !isEditModeAlumno && showPlanId !== {{ $alumno->id }}"
                    x-cloak
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

                    <div class="mt-4 flex justify-end">
                        <button type="button" x-on:click="isEditModeAlumno = true" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Editar</button>
                    </div>

                    <div class="mt-6">
                        <!-- Static Calendar -->
                        <div class="bg-gray-100 p-4 rounded"
                             x-data='calendar(
                                @json($attendanceData),
                                "{{ $alumno->fecha_inicio->format('Y-m-d') }}",
                                "{{ $alumno->fecha_termino->format('Y-m-d') }}",
                                @json($diasActivos)
                             )'
                             x-init="init()">
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
                                    <div class="py-1 flex justify-center" :class="{ 'opacity-50': date.disabled }">
                                        <div class="w-6 h-6 flex items-center justify-center rounded-full"
                                             :class="{
                                                 'bg-green-600 text-white': date.estado === 'asistencia',
                                                 'bg-red-600 text-white': date.estado === 'falta',
                                                 'bg-blue-600 text-white': date.estado === 'justificado',
                                                 'bg-gray-400 text-white': date.estado === 'no_lista'
                                             }">
                                            <span x-text="date.day"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>
                    @php $maxDate = min($alumno->fecha_termino->format('Y-m-d'), now()->toDateString()); @endphp
                    <div class="mt-4" x-data="editAttendance(
                            @json($attendanceData),
                            '{{ $alumno->fecha_inicio->format('Y-m-d') }}',
                            '{{ $alumno->fecha_termino->format('Y-m-d') }}',
                            @json($diasActivos)
                        )" x-init="fecha='{{ now()->toDateString() }}'; updateEstado()">
                        <form method="POST" action="{{ route('moderador.guardarListaAlumno', $alumno) }}" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fecha</label>
                                <input type="date" name="fecha" x-model="fecha" @change="updateEstado()" min="{{ $alumno->fecha_inicio->format('Y-m-d') }}" max="{{ $maxDate }}" class="mt-1 block w-full rounded-md border-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Estado</label>
                                <select name="estado" x-model="estado" class="mt-1 block w-full rounded-md border-gray-300">
                                    <option value="asistencia">Asistencia</option>
                                    <option value="falta">Falta</option>
                                    <option value="justificado">Justificado</option>
                                </select>
                            </div>
                            <button type="submit" :disabled="!isValidDate(fecha)" class="px-4 py-2 bg-green-600 text-white rounded disabled:opacity-50">Guardar lista</button>
                        </form>
                    </div>
                    @if($alumno->plan)
                        @php
                            $totalSubtemas = $alumno->plan->temas->sum(fn($t) => $t->subtemas->count());
                            $entregadas = $alumno->entregas->count();
                            $avance = $totalSubtemas ? intval($entregadas * 100 / $totalSubtemas) : 0;
                        @endphp
                        <div class="mt-4 bg-gray-50 p-4 rounded">
                            <h4 class="font-medium">Plan: {{ $alumno->plan->nombre }}</h4>
                            <p class="text-sm text-gray-700 mb-2">Actividades entregadas: {{ $entregadas }} / {{ $totalSubtemas }}</p>
                            <div class="w-full h-2 bg-gray-200 rounded">
                                <div class="h-2 bg-blue-600 rounded" style="width: {{ $avance }}%"></div>
                            </div>
                            <button type="button" x-on:click="showPlanId = {{ $alumno->id }}" class="mt-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Ver plan</button>
                        </div>
                    @endif
                    <div class="mt-4 text-right">
                        <button type="button" x-on:click="editIdAlumno = null" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">Cerrar</button>
                    </div>
                </div>

                <!-- Edit Form -->
                <div
                    x-show="editIdAlumno === {{ $alumno->id }} && isEditModeAlumno && showPlanId !== {{ $alumno->id }}"
                    x-cloak
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
                <div
                    x-show="showPlanId === {{ $alumno->id }}"
                    x-cloak
                    class="bg-white rounded-lg shadow-md p-6 overflow-y-auto max-h-[80vh]"
                >
                    @include('moderador.partials.plan_vista', ['alumno' => $alumno])
                    <div class="mt-4 text-right">
                        <button type="button" x-on:click="showPlanId = null" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cerrar</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script>
        function calendar(records = [], start = null, end = null, activeDays = []) {
            return {
                currentDate: new Date(),
                dayNames: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
                monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
                blanks: [],
                dates: [],
                records: records,
                startDate: start ? new Date(start) : null,
                endDate: end ? new Date(end) : null,
                activeDays: activeDays,
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

                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    this.dates = Array.from({ length: total }, (_, i) => {
                        const day = i + 1;
                        const dateObj = new Date(this.currentYear, this.currentMonth, day);
                        dateObj.setHours(0, 0, 0, 0);
                        const dateStr = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
                        const rec = this.records.find(r => r.fecha === dateStr);
                        let estado = rec ? rec.estado : null;
                        const inRange = (!this.startDate || dateObj >= this.startDate) && (!this.endDate || dateObj <= this.endDate);
                        const shouldAttend = inRange && this.activeDays.includes(dateObj.getDay());
                        const isPastOrToday = dateObj <= today;
                        if (shouldAttend && !rec && isPastOrToday) {
                            estado = 'no_lista';
                        }
                        const disabled = !inRange || dateObj > today || !shouldAttend;
                        return { day: day, estado: estado, disabled: disabled };
                    });
                }
            };
        }
        function editAttendance(records = [], start = null, end = null, activeDays = []) {
            return {
                records: records,
                fecha: '',
                estado: 'asistencia',
                startDate: start ? new Date(start) : null,
                endDate: end ? new Date(end) : null,
                activeDays: activeDays,
                isValidDate(dateStr) {
                    if (!dateStr) return false;
                    const d = new Date(dateStr);
                    d.setHours(0,0,0,0);
                    const today = new Date();
                    today.setHours(0,0,0,0);
                    if (this.startDate && d < this.startDate) return false;
                    if (this.endDate && d > this.endDate) return false;
                    if (d > today) return false;
                    if (!this.activeDays.includes(d.getDay())) return false;
                    return true;
                },
                updateEstado() {
                    if (!this.isValidDate(this.fecha)) { this.estado = 'asistencia'; return; }
                    const rec = this.records.find(r => r.fecha === this.fecha);
                    this.estado = rec ? rec.estado : 'asistencia';
                }
            };
        }
    </script>
</div>  