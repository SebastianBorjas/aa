@push('vite')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endpush

<div class="flex flex-col md:flex-row gap-6">
    <!-- Instituciones Table (Left on Desktop, Top on Mobile) -->
    <div class="w-full md:w-1/2">
        <div class="flex flex-col sm:flex-row gap-3 mb-4">
            <button class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition w-full sm:w-auto">+ Institución</button>
            <button class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition w-full sm:w-auto">+ Maestro</button>
        </div>
        <div class="bg-white rounded-lg shadow overflow-x-auto" x-data="{ expanded: null }">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-50 text-xs uppercase font-medium text-gray-600">
                    <tr>
                        <th class="px-4 py-2">Nombre de la Institución</th>
                        <th class="px-4 py-2">Correo</th>
                        <th class="px-4 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <!-- Sample Institución 1 -->
                    <tr @click="expanded === 1 ? expanded = null : expanded = 1" class="cursor-pointer hover:bg-gray-50 transition">
                        <td class="px-4 py-2 font-medium flex items-center gap-2">
                            <span x-text="expanded === 1 ? '▼' : '➕'"></span> Instituto Central
                        </td>
                        <td class="px-4 py-2 text-gray-600">instituto.central@example.com</td>
                        <td class="px-4 py-2 text-right">
                            <div class="flex justify-end gap-2">
                                <i class="fas fa-pencil-alt text-blue-500"></i>
                                <i class="fas fa-trash-alt text-red-500"></i>
                            </div>
                        </td>
                    </tr>
                    <tr x-show="expanded === 1" x-transition x-cloak class="bg-gray-50">
                        <td colspan="3" class="px-4 py-2 max-h-60 overflow-y-auto">
                            <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-1 text-left text-gray-600">Nombre</th>
                                        <th class="px-3 py-1 text-left text-gray-600">Correo</th>
                                        <th class="px-3 py-1 text-left text-gray-600">Teléfono</th>
                                        <th class="px-3 py-1 text-right text-gray-600">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-1 text-gray-800">María López</td>
                                        <td class="px-3 py-1 text-gray-600">maria.lopez@example.com</td>
                                        <td class="px-3 py-1 text-gray-600">123-456-7890</td>
                                        <td class="px-3 py-1 text-right">
                                            <div class="flex justify-end gap-2">
                                                <i class="fas fa-pencil-alt text-blue-500"></i>
                                                <i class="fas fa-trash-alt text-red-500"></i>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-1 text-gray-800">Carlos Ramírez</td>
                                        <td class="px-3 py-1 text-gray-600">carlos.ramirez@example.com</td>
                                        <td class="px-3 py-1 text-gray-600">987-654-3210</td>
                                        <td class="px-3 py-1 text-right">
                                            <div class="flex justify-end gap-2">
                                                <i class="fas fa-pencil-alt text-blue-500"></i>
                                                <i class="fas fa-trash-alt text-red-500"></i>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <!-- Sample Institución 2 -->
                    <tr @click="expanded === 2 ? expanded = null : expanded = 2" class="cursor-pointer hover:bg-gray-50 transition">
                        <td class="px-4 py-2 font-medium flex items-center gap-2">
                            <span x-text="expanded === 2 ? '▼' : '➕'"></span> Instituto Norte
                        </td>
                        <td class="px-4 py-2 text-gray-600">instituto.norte@example.com</td>
                        <td class="px-4 py-2 text-right">
                            <div class="flex justify-end gap-2">
                                <i class="fas fa-pencil-alt text-blue-500"></i>
                                <i class="fas fa-trash-alt text-red-500"></i>
                            </div>
                        </td>
                    </tr>
                    <tr x-show="expanded === 2" x-transition x-cloak class="bg-gray-50">
                        <td colspan="3" class="px-4 py-2 max-h-60 overflow-y-auto">
                            <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-1 text-left text-gray-600">Nombre</th>
                                        <th class="px-3 py-1 text-left text-gray-600">Correo</th>
                                        <th class="px-3 py-1 text-left text-gray-600">Teléfono</th>
                                        <th class="px-3 py-1 text-right text-gray-600">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-1 text-gray-800">Ana Gómez</td>
                                        <td class="px-3 py-1 text-gray-600">ana.gomez@example.com</td>
                                        <td class="px-3 py-1 text-gray-600">555-123-4567</td>
                                        <td class="px-3 py-1 text-right">
                                            <div class="flex justify-end gap-2">
                                                <i class="fas fa-pencil-alt text-blue-500"></i>
                                                <i class="fas fa-trash-alt text-red-500"></i>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <!-- Sample Institución 3 -->
                    <tr @click="expanded === 3 ? expanded = null : expanded = 3" class="cursor-pointer hover:bg-gray-50 transition">
                        <td class="px-4 py-2 font-medium flex items-center gap-2">
                            <span x-text="expanded === 3 ? '▼' : '➕'"></span> Instituto Sur
                        </td>
                        <td class="px-4 py-2 text-gray-600">instituto.sur@example.com</td>
                        <td class="px-4 py-2 text-right">
                            <div class="flex justify-end gap-2">
                                <i class="fas fa-pencil-alt text-blue-500"></i>
                                <i class="fas fa-trash-alt text-red-500"></i>
                            </div>
                        </td>
                    </tr>
                    <tr x-show="expanded === 3" x-transition x-cloak class="bg-gray-50">
                        <td colspan="3" class="px-4 py-2 max-h-60 overflow-y-auto">
                            <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-1 text-left text-gray-600">Nombre</th>
                                        <th class="px-3 py-1 text-left text-gray-600">Teléfono</th>
                                        <th class="px-3 py-1 text-left text-gray-600">Correo</th>
                                        <th class="px-3 py-1 text-right text-gray-600">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-1 text-gray-800">Pedro Martínez</td>
                                        <td class="px-3 py-1 text-gray-600">444-987-6543</td>
                                        <td class="px-3 py-1 text-gray-600">pedro.martinez@example.com</td>
                                        <td class="px-3 py-1 text-right">
                                            <div class="flex justify-end gap-2">
                                                <i class="fas fa-pencil-alt text-blue-500"></i>
                                                <i class="fas fa-trash-alt text-red-500"></i>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Empresas Table (Right on Desktop, Bottom on Mobile) -->
    <div class="w-full md:w-1/2">
        <div class="flex flex-col sm:flex-row gap-3 mb-4">
            <button class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition w-full sm:w-auto">+ Empresa</button>
        </div>
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-50 text-xs uppercase font-medium text-gray-600">
                    <tr>
                        <th class="px-4 py-2">Nombre de la Empresa</th>
                        <th class="px-4 py-2">Correo</th>
                        <th class="px-4 py-2">Teléfono</th>
                        <th class="px-4 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <!-- Sample Empresa 1 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-2 font-medium">Tech Solutions</td>
                        <td class="px-4 py-2 text-gray-600">tech.solutions@example.com</td>
                        <td class="px-4 py-2 text-gray-600">111-222-3333</td>
                        <td class="px-4 py-2 text-right">
                            <div class="flex justify-end gap-2">
                                <i class="fas fa-pencil-alt text-blue-500"></i>
                                <i class="fas fa-trash-alt text-red-500"></i>
                            </div>
                        </td>
                    </tr>
                    <!-- Sample Empresa 2 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-2 font-medium">Global Corp</td>
                        <td class="px-4 py-2 text-gray-600">global.corp@example.com</td>
                        <td class="px-4 py-2 text-gray-600">444-555-6666</td>
                        <td class="px-4 py-2 text-right">
                            <div class="flex justify-end gap-2">
                                <i class="fas fa-pencil-alt text-blue-500"></i>
                                <i class="fas fa-trash-alt text-red-500"></i>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
    [x-cloak] { display: none; }
</style>