@php
    use App\Models\Empresa;
    use App\Models\Moderador;
    use Illuminate\Support\Facades\Auth;

    // Get the moderator's id_plantel
    $moderador = Moderador::where('id_user', Auth::id())->first();
    // Fetch empresas for the moderator's id_plantel
    $empresas = $moderador ? Empresa::where('id_plantel', $moderador->id_plantel)->with('user')->get() : collect();
@endphp

<div class="max-w-7xl mx-auto p-4" x-data="{ isFormOpenEmpresa: {{ $errors->any() && old('section') == 'empresa' ? 'true' : 'false' }}, editIdEmpresa: null }">
    <div class="flex flex-col lg:flex-row lg:space-x-6 space-y-6 lg:space-y-0">
        <!-- Left Side: Table -->
        <div class="w-full lg:w-2/3 bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Lista de Empresas</h2>
                <button 
                    x-show="!isFormOpenEmpresa && !editIdEmpresa" x-cloak
                    x-on:click="isFormOpenEmpresa = true; editIdEmpresa = null" 
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition font-medium"
                >
                    Registrar Nueva Empresa
                </button>
            </div>

            <!-- Success/Error Messages -->
            @if (session('success') && session('tab') === 'empresas')
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any() && old('section') == 'empresa')
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Empresas Table -->
            @if ($empresas->isEmpty())
                <p class="text-gray-600">No hay empresas registradas.</p>
            @else
                <div class="overflow-x-auto max-w-full">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-700 text-white">
                            <tr>
                                <th class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider">Nombre</th>
                                <th class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider">Correo</th>
                                <th class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider">Teléfono</th>
                                <th class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider">Creado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-300">
                            @foreach ($empresas as $empresa)
                                <tr 
                                    x-on:click="editIdEmpresa = editIdEmpresa === {{ $empresa->id }} ? null : {{ $empresa->id }}; isFormOpenEmpresa = false" 
                                    class="cursor-pointer hover:bg-gray-200 transition"
                                    :class="{ 'bg-gray-200': editIdEmpresa === {{ $empresa->id }} }"
                                >
                                    <td class="px-2 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $empresa->name }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">{{ $empresa->user->email }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">{{ $empresa->telefono }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">{{ $empresa->created_at->format('d/m/Y') }}</td>
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
                x-show="isFormOpenEmpresa && !editIdEmpresa" x-cloak
                class="bg-white rounded-lg shadow-md p-6"
            >
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Registrar Empresa</h2>
                <form method="POST" action="{{ route('moderador.registerEmpresa') }}">
                    @csrf
                    <input type="hidden" name="section" value="empresa">
                    <div class="space-y-4">
                        <div>
                            <label for="nombre_empresa" class="block text-sm font-medium text-gray-700">Nombre de la Empresa</label>
                            <input 
                                type="text" 
                                id="nombre_empresa" 
                                name="nombre" 
                                value="{{ old('nombre') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                placeholder="Ingresa el nombre"
                                required
                            >
                        </div>
                        <div>
                            <label for="correo_empresa" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                            <input 
                                type="email" 
                                id="correo_empresa" 
                                name="correo" 
                                value="{{ old('correo') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                placeholder="Ingresa el correo"
                                required
                            >
                        </div>
                        <div>
                            <label for="contrasena_empresa" class="block text-sm font-medium text-gray-700">Contraseña</label>
                            <input 
                                type="password" 
                                id="contrasena_empresa" 
                                name="contrasena"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                placeholder="Ingresa la contraseña"
                                required
                            >
                        </div>
                        <div>
                            <label for="contrasena_confirmation_empresa" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                            <input 
                                type="password" 
                                id="contrasena_confirmation_empresa" 
                                name="contrasena_confirmation"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                placeholder="Confirma la contraseña"
                                required
                            >
                        </div>
                        <div>
                            <label for="telefono_empresa" class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input 
                                type="text" 
                                id="telefono_empresa" 
                                name="telefono" 
                                value="{{ old('telefono') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                placeholder="Ingresa el teléfono"
                                required
                            >
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button 
                            type="button"
                            x-on:click="isFormOpenEmpresa = false" 
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
            @foreach ($empresas as $empresa)
                <div 
                    x-show="editIdEmpresa === {{ $empresa->id }}" x-cloak
                    class="bg-white rounded-lg shadow-md p-6"
                >
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Editar Empresa</h3>
                    <form method="POST" action="{{ route('moderador.updateEmpresa', $empresa) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="section" value="empresa">
                        <div class="space-y-4">
                            <div>
                                <label for="nombre_empresa_{{ $empresa->id }}" class="block text-sm font-medium text-gray-700">Nombre</label>
                                <input 
                                    type="text" 
                                    id="nombre_empresa_{{ $empresa->id }}" 
                                    name="nombre" 
                                    value="{{ old('nombre', $empresa->name) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    required
                                >
                            </div>
                            <div>
                                <label for="correo_empresa_{{ $empresa->id }}" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                                <input 
                                    type="email" 
                                    id="correo_empresa_{{ $empresa->id }}" 
                                    name="correo" 
                                    value="{{ old('correo', $empresa->user->email) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    required
                                >
                            </div>
                            <div>
                                <label for="contrasena_empresa_{{ $empresa->id }}" class="block text-sm font-medium text-gray-700">Nueva Contraseña (Opcional)</label>
                                <input 
                                    type="password" 
                                    id="contrasena_empresa_{{ $empresa->id }}" 
                                    name="contrasena"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    placeholder="Ingresa nueva contraseña"
                                >
                            </div>
                            <div>
                                <label for="contrasena_confirmation_empresa_{{ $empresa->id }}" class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
                                <input 
                                    type="password" 
                                    id="contrasena_confirmation_empresa_{{ $empresa->id }}" 
                                    name="contrasena_confirmation"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    placeholder="Confirma la contraseña"
                                >
                            </div>
                            <div>
                                <label for="telefono_empresa_{{ $empresa->id }}" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                <input 
                                    type="text" 
                                    id="telefono_empresa_{{ $empresa->id }}" 
                                    name="telefono" 
                                    value="{{ old('telefono', $empresa->telefono) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                    required
                                >
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button 
                                type="button"
                                x-on:click="editIdEmpresa = null" 
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
                                x-on:click="if (confirm('¿Estás seguro de eliminar esta empresa?')) { $refs.deleteFormEmpresa{{ $empresa->id }}.submit(); }"
                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
                            >
                                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12M10 3h4" />
                                </svg>
                            </button>
                        </div>
                    </form>
                    <form 
                        x-ref="deleteFormEmpresa{{ $empresa->id }}"
                        method="POST" 
                        action="{{ route('moderador.deleteEmpresa', $empresa) }}" 
                        class="hidden"
                    >
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="section" value="empresa">
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</div>