<div class="container mx-auto">
    <!-- Success Message -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Students Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-4 border-b">Nombre</th>
                    <th class="py-2 px-4 border-b">Correo</th>
                    <th class="py-2 px-4 border-b">Teléfono</th>
                    <th class="py-2 px-4 border-b">Teléfono Emergencia</th>
                    <th class="py-2 px-4 border-b">Días</th>
                    <th class="py-2 px-4 border-b">Fecha Inicio</th>
                    <th class="py-2 px-4 border-b">Fecha Término</th>
                    <th class="py-2 px-4 border-b">Empresa</th>
                    <th class="py-2 px-4 border-b">Institución</th>
                    <th class="py-2 px-4 border-b">Maestro</th>
                    <th class="py-2 px-4 border-b">Especialidad</th>
                    <th class="py-2 px-4 border-b">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $moderador = App\Models\Moderador::where('id_user', Auth::id())->first();
                    $alumnos = App\Models\Alumno::where('id_plantel', $moderador->id_plantel)->with(['user', 'empresa', 'institucion', 'maestro', 'especialidad'])->get();
                @endphp
                @forelse ($alumnos as $alumno)
                    <tr>
                        <td class="py-2 px-4 border-b">{{ $alumno->name }}</td>
                        <td class="py-2 px-4 border-b">{{ $alumno->user->email }}</td>
                        <td class="py-2 px-4 border-b">{{ $alumno->telefono }}</td>
                        <td class="py-2 px-4 border-b">{{ $alumno->telefono_emergencia }}</td>
                        <td class="py-2 px-4 border-b">
                            {{ collect(['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'])
                                ->filter(fn($day) => $alumno->$day)
                                ->map(fn($day) => ucfirst($day))
                                ->implode(', ') }}
                        </td>
                        <td class="py-2 px-4 border-b">{{ $alumno->fecha_inicio->format('Y-m-d') }}</td>
                        <td class="py-2 px-4 border-b">{{ $alumno->fecha_termino->format('Y-m-d') }}</td>
                        <td class="py-2 px-4 border-b">{{ $alumno->empresa->name ?? 'N/A' }}</td>
                        <td class="py-2 px-4 border-b">{{ $alumno->institucion->name ?? 'N/A' }}</td>
                        <td class="py-2 px-4 border-b">{{ $alumno->maestro->name ?? 'N/A' }}</td>
                        <td class="py-2 px-4 border-b">{{ $alumno->especialidad->name ?? 'N/A' }}</td>
                        <td class="py-2 px-4 border-b">
                            <form action="{{ route('moderador.updateAlumno', $alumno) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="text-blue-600 hover:underline">Editar</button>
                            </form>
                            <form action="{{ route('moderador.deleteAlumno', $alumno) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('¿Estás seguro de eliminar este alumno?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="py-2 px-4 border-b text-center">No hay alumnos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Register Student Form -->
    <div class="mt-8 bg-gray-50 p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Registrar Alumno</h2>
        <!-- Replace the form section in alumnos.blade.php -->
        <form method="POST" action="{{ route('moderador.registerAlumno') }}" x-data="{
            institucionId: '',
            maestros: [],
            especialidades: [],
            lunes: false,
            martes: false,
            miercoles: false,
            jueves: false,
            viernes: false,
            sabado: false,
            domingo: false,
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
            },
            submitForm() {
                this.$el.submit(); // Submit the form programmatically
            }
        }" @submit.prevent="submitForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Email -->
                <div>
                    <label for="alumno_correo" class="block text-sm font-medium">Correo</label>
                    <input type="email" name="correo" id="alumno_correo" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <!-- Password -->
                <div>
                    <label for="alumno_contrasena" class="block text-sm font-medium">Contraseña</label>
                    <input type="password" name="contrasena" id="alumno_contrasena" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <!-- Password Confirmation -->
                <div>
                    <label for="alumno_contrasena_confirmation" class="block text-sm font-medium">Confirmar Contraseña</label>
                    <input type="password" name="contrasena_confirmation" id="alumno_contrasena_confirmation" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <!-- Name -->
                <div>
                    <label for="alumno_nombre" class="block text-sm font-medium">Nombre</label>
                    <input type="text" name="nombre" id="alumno_nombre" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <!-- Phone -->
                <div>
                    <label for="alumno_telefono" class="block text-sm font-medium">Teléfono</label>
                    <input type="text" name="telefono" id="alumno_telefono" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <!-- Emergency Phone -->
                <div>
                    <label for="alumno_telefono_emergencia" class="block text-sm font-medium">Teléfono de Emergencia</label>
                    <input type="text" name="telefono_emergencia" id="alumno_telefono_emergencia" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <!-- Days -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium">Días de Clase</label>
                    <div class="flex gap-2 mt-1">
                        @foreach (['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'] as $day)
                            <button type="button" x-on:click="{{ $day }} = !{{ $day }}" :class="{{ $day }} ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'" class="px-3 py-1 rounded-md">{{ ucfirst($day) }}</button>
                            <input type="hidden" name="{{ $day }}" x-bind:value="{{ $day }} ? '1' : '0'">
                        @endforeach
                    </div>
                </div>
                <!-- Start Date -->
                <div>
                    <label for="alumno_fecha_inicio" class="block text-sm font-medium">Fecha de Inicio</label>
                    <input type="date" name="fecha_inicio" id="alumno_fecha_inicio" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <!-- End Date -->
                <div>
                    <label for="alumno_fecha_termino" class="block text-sm font-medium">Fecha de Término</label>
                    <input type="date" name="fecha_termino" id="alumno_fecha_termino" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <!-- Empresa -->
                <div>
                    <label for="alumno_id_empresa" class="block text-sm font-medium">Empresa</label>
                    <select name="id_empresa" id="alumno_id_empresa" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Seleccione una empresa</option>
                        @php
                            $moderador = App\Models\Moderador::where('id_user', Auth::id())->first();
                            $empresas = App\Models\Empresa::where('id_plantel', $moderador->id_plantel)->get();
                        @endphp
                        @foreach ($empresas as $empresa)
                            <option value="{{ $empresa->id }}">{{ $empresa->name }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Institucion -->
                <div>
                    <label for="alumno_id_institucion" class="block text-sm font-medium">Institución</label>
                    <select name="id_institucion" id="alumno_id_institucion" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" x-model="institucionId" @change="fetchMaestros()">
                        <option value="">Seleccione una institución</option>
                        @php
                            $instituciones = App\Models\Institucion::where('id_plantel', $moderador->id_plantel)->get();
                        @endphp
                        @foreach ($instituciones as $institucion)
                            <option value="{{ $institucion->id }}">{{ $institucion->name }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Maestro -->
                <div>
                    <label for="alumno_id_maestro" class="block text-sm font-medium">Maestro</label>
                    <select name="id_maestro" id="alumno_id_maestro" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" :disabled="!institucionId">
                        <option value="">Seleccione un maestro</option>
                        <template x-for="maestro in maestros" :key="maestro.id">
                            <option :value="maestro.id" x-text="maestro.name"></option>
                        </template>
                    </select>
                </div>
                <!-- Especialidad -->
                <div>
                    <label for="alumno_id_especialidad" class="block text-sm font-medium">Especialidad</label>
                    <select name="id_especialidad" id="alumno_id_especialidad" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" :disabled="!institucionId">
                        <option value="">Seleccione una especialidad</option>
                        <template x-for="especialidad in especialidades" :key="especialidad.id">
                            <option :value="especialidad.id" x-text="especialidad.name"></option>
                        </template>
                    </select>
                </div>
            </div>
            <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Registrar</button>
        </form>
    </div>
</div>