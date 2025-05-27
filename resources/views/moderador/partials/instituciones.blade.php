@php
    // Hardcoded data for demonstration
    $instituciones = [
        (object)[
            'id' => 1,
            'nombre' => 'Escuela Primaria Benito Juárez',
            'direccion' => 'Av. Reforma 123, Ciudad de México',
            'telefono' => '55-1234-5678',
            'maestros' => [
                (object)['id' => 1, 'nombre' => 'Juan Pérez', 'email' => 'juan.perez@escuela.mx', 'materia' => 'Matemáticas'],
                (object)['id' => 2, 'nombre' => 'María López', 'email' => 'maria.lopez@escuela.mx', 'materia' => 'Español'],
            ],
            'especialidades' => [
                (object)['id' => 1, 'nombre' => 'Matemáticas Avanzadas', 'descripcion' => 'Curso intensivo de cálculo'],
                (object)['id' => 2, 'nombre' => 'Literatura', 'descripcion' => 'Estudio de literatura clásica'],
            ],
        ],
        (object)[
            'id' => 2,
            'nombre' => 'Colegio San Ignacio',
            'direccion' => 'Calle Independencia 456, Guadalajara',
            'telefono' => '33-9876-5432',
            'maestros' => [
                (object)['id' => 3, 'nombre' => 'Carlos Gómez', 'email' => 'carlos.gomez@escuela.mx', 'materia' => 'Ciencias'],
                (object)['id' => 4, 'nombre' => 'Ana Martínez', 'email' => 'ana.martinez@escuela.mx', 'materia' => 'Historia'],
            ],
            'especialidades' => [
                (object)['id' => 3, 'nombre' => 'Física', 'descripcion' => 'Estudio de mecánica y termodinámica'],
                (object)['id' => 4, 'nombre' => 'Historia Universal', 'descripcion' => 'Análisis de eventos históricos'],
            ],
        ],
    ];
@endphp

<div class="max-w-7xl mx-auto p-4" x-data="{
    selectedInstitucion: null,
    selectedMaestro: null,
    selectedEspecialidad: null,
    expandedRow: null
}">
    <div class="flex flex-col lg:flex-row lg:space-x-6 space-y-6 lg:space-y-0">
        <!-- Left Side: Instituciones Table -->
        <div class="w-full lg:w-2/3 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Lista de Instituciones</h2>
            <div class="overflow-x-auto max-w-full">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-700 text-white">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Dirección</th>
                            <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Teléfono</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-300">
                        @foreach ($instituciones as $institucion)
                            <tr 
                                x-on:click="selectedInstitucion = {{ $institucion->id }}; selectedMaestro = null; selectedEspecialidad = null; expandedRow = expandedRow === {{ $institucion->id }} ? null : {{ $institucion->id }}"
                                class="cursor-pointer hover:bg-gray-200 transition"
                                :class="{ 'bg-gray-200': selectedInstitucion === {{ $institucion->id }} }"
                            >
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $institucion->nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $institucion->direccion }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $institucion->telefono }}</td>
                            </tr>
                            <!-- Sub-table for Maestros and Especialidades -->
                            <tr x-show="expandedRow === {{ $institucion->id }}" x-cloak>
                                <td colspan="3" class="px-6 py-4">
                                    <div class="bg-gray-100 rounded-lg p-4 max-w-full">
                                        <!-- Maestros Section -->
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Maestros</h3>
                                        <div class="overflow-x-auto max-w-full mb-4">
                                            <table class="min-w-full divide-y divide-gray-300">
                                                <thead class="bg-gray-600 text-white">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left text-xs font-bold uppercase tracking-wider">Nombre</th>
                                                        <th class="px-4 py-2 text-left text-xs font-bold uppercase tracking-wider">Email</th>
                                                        <th class="px-4 py-2 text-left text-xs font-bold uppercase tracking-wider">Materia</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-300">
                                                    @foreach ($institucion->maestros as $maestro)
                                                        <tr 
                                                            x-on:click="selectedMaestro = {{ $maestro->id }}; selectedEspecialidad = null; selectedInstitucion = null"
                                                            class="cursor-pointer hover:bg-gray-200 transition"
                                                            :class="{ 'bg-gray-200': selectedMaestro === {{ $maestro->id }} }"
                                                        >
                                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ $maestro->nombre }}</td>
                                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $maestro->email }}</td>
                                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $maestro->materia }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <!-- Especialidades Section -->
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Especialidades</h3>
                                        <div class="overflow-x-auto max-w-full">
                                            <table class="min-w-full divide-y divide-gray-300">
                                                <thead class="bg-gray-600 text-white">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left text-xs font-bold uppercase tracking-wider">Nombre</th>
                                                        <th class="px-4 py-2 text-left text-xs font-bold uppercase tracking-wider">Descripción</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-300">
                                                    @foreach ($institucion->especialidades as $especialidad)
                                                        <tr 
                                                            x-on:click="selectedEspecialidad = {{ $especialidad->id }}; selectedMaestro = null; selectedInstitucion = null"
                                                            class="cursor-pointer hover:bg-gray-200 transition"
                                                            :class="{ 'bg-gray-200': selectedEspecialidad === {{ $especialidad->id }} }"
                                                        >
                                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ $especialidad->nombre }}</td>
                                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $especialidad->descripcion }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Side: Details Panel -->
        <div class="w-full lg:w-1/3 bg-white rounded-lg shadow-md p-6">
            <div x-show="selectedInstitucion">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Detalles de la Institución</h2>
                @foreach ($instituciones as $institucion)
                    <div x-show="selectedInstitucion === {{ $institucion->id }}" x-cloak>
                        <p class="text-sm text-gray-700"><strong>Nombre:</strong> {{ $institucion->nombre }}</p>
                        <p class="text-sm text-gray-700"><strong>Dirección:</strong> {{ $institucion->direccion }}</p>
                        <p class="text-sm text-gray-700"><strong>Teléfono:</strong> {{ $institucion->telefono }}</p>
                    </div>
                @endforeach
            </div>
            <div x-show="selectedMaestro">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Detalles del Maestro</h2>
                @foreach ($instituciones as $institucion)
                    @foreach ($institucion->maestros as $maestro)
                        <div x-show="selectedMaestro === {{ $maestro->id }}" x-cloak>
                            <p class="text-sm text-gray-700"><strong>Nombre:</strong> {{ $maestro->nombre }}</p>
                            <p class="text-sm text-gray-700"><strong>Email:</strong> {{ $maestro->email }}</p>
                            <p class="text-sm text-gray-700"><strong>Materia:</strong> {{ $maestro->materia }}</p>
                        </div>
                    @endforeach
                @endforeach
            </div>
            <div x-show="selectedEspecialidad">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Detalles de la Especialidad</h2>
                @foreach ($instituciones as $institucion)
                    @foreach ($institucion->especialidades as $especialidad)
                        <div x-show="selectedEspecialidad === {{ $especialidad->id }}" x-cloak>
                            <p class="text-sm text-gray-700"><strong>Nombre:</strong> {{ $especialidad->nombre }}</p>
                            <p class="text-sm text-gray-700"><strong>Descripción:</strong> {{ $especialidad->descripcion }}</p>
                        </div>
                    @endforeach
                @endforeach
            </div>
            <div x-show="!selectedInstitucion && !selectedMaestro && !selectedEspecialidad" class="text-gray-600">
                Selecciona una institución, maestro o especialidad para ver los detalles.
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('instituciones', () => ({
            selectedInstitucion: null,
            selectedMaestro: null,
            selectedEspecialidad: null,
            expandedRow: null
        }));
    });
</script>