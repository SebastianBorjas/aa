@push('vite')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endpush

<div class="flex flex-col md:flex-row gap-6">
    <!-- Alumnos Table (Left on Desktop, Top on Mobile) -->
    <div class="w-full md:w-1/2">
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-50 text-xs uppercase font-medium text-gray-600">
                    <tr>
                        <th class="px-4 py-2">Nombre</th>
                        <th class="px-4 py-2">Institución</th>
                        <th class="px-4 py-2">Empresa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <!-- Sample Alumno 1 (Selected for Dashboard) -->
                    <tr class="bg-gray-100 transition">
                        <td class="px-4 py-2 font-medium">Juan Pérez</td>
                        <td class="px-4 py-2 text-gray-600">Instituto Central</td>
                        <td class="px-4 py-2 text-gray-600">Tech Solutions</td>
                    </tr>
                    <!-- Sample Alumno 2 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-2 font-medium">Sofía Gómez</td>
                        <td class="px-4 py-2 text-gray-600">Instituto Norte</td>
                        <td class="px-4 py-2 text-gray-600">Global Corp</td>
                    </tr>
                    <!-- Sample Alumno 3 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-2 font-medium">Luis Martínez</td>
                        <td class="px-4 py-2 text-gray-600">Instituto Sur</td>
                        <td class="px-4 py-2 text-gray-600">Tech Solutions</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dashboard (Right on Desktop, Bottom on Mobile) -->
    <div class="w-full md:w-1/2">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Detalles del Alumno</h2>
                <div class="flex gap-2">
                    <i class="fas fa-pencil-alt text-blue-500 text-lg"></i>
                    <i class="fas fa-trash-alt text-red-500 text-lg"></i>
                </div>
            </div>
            <div class="space-y-4">
                <!-- Sample Alumno Details -->
                <div>
                    <p class="text-sm font-medium text-gray-600">Nombre</p>
                    <p class="text-gray-800">Juan Pérez</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Correo</p>
                    <p class="text-gray-800">juan.perez@example.com</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Teléfono</p>
                    <p class="text-gray-800">123-456-7890</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Calificación</p>
                    <p class="text-gray-800">8.5</p>
                </div>
                <!-- Static Aesthetic Chart -->
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-2">Progreso</p>
                    <canvas id="fakeChart" style="height: 150px;"></canvas>
                    <style>
                        #fakeChart {
                            background: linear-gradient(to right, #4ade80 30%, #22c55e 60%, #15803d 100%);
                            border-radius: 8px;
                            width: 100%;
                        }
                    </style>
                </div>
            </div>
        </div>
    </div>
</div>