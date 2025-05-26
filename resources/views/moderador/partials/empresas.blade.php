@php
    use App\Models\Empresa;
    use App\Models\Moderador;
    use Illuminate\Support\Facades\Auth;

    // Get the moderator's id_plantel
    $moderador = Moderador::where('id_user', Auth::id())->first();
    // Fetch empresas for the moderator's id_plantel
    $empresas = $moderador ? Empresa::where('id_plantel', $moderador->id_plantel)->with('user')->get() : collect();
@endphp

<div class="p-4" x-data="{ isFormOpen: {{ $errors->any() ? 'true' : 'false' }}, editId: null, isRegister: {{ $errors->any() ? 'true' : 'false' }} }">
  <div class="flex flex-col md:flex-row md:space-x-6">
    <!-- Left Side: Table -->
    <div class="w-full md:w-1/2 bg-white rounded-lg shadow-md p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-900">Lista de Empresas</h2>
        <!-- Register Button (Desktop Only) -->
        <button 
          x-show="!isRegister && !editId" 
          x-on:click="isRegister = true; editId = null" 
          class="hidden md:block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition font-medium"
        >
          Registrar Nueva Empresa
        </button>
        <!-- Register Button (Mobile Only) -->
        <button 
          x-on:click="isFormOpen = !isFormOpen; isRegister = !isFormOpen; editId = null" 
          class="md:hidden px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition font-medium"
        >
          <span x-text="isFormOpen && isRegister ? 'Ocultar Formulario' : 'Registrar Nueva Empresa'"></span>
        </button>
      </div>

      <!-- Success/Error Messages -->
      @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
          {{ session('success') }}
        </div>
      @endif
      @if ($errors->any())
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
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider md:hidden">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              @foreach ($empresas as $empresa)
                <tr 
                  x-on:click="editId = editId === {{ $empresa->id }} ? null : {{ $empresa->id }}; isFormOpen = false; isRegister = false" 
                  class="cursor-pointer hover:bg-gray-100 transition md:hover:bg-gray-100"
                  :class="{ 'bg-gray-100': editId === {{ $empresa->id }} }"
                >
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $empresa->name }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $empresa->user->email }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $empresa->telefono }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $empresa->created_at->format('d/m/Y') }}</td>
                  <!-- Edit Button (Mobile Only) -->
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium md:hidden">
                    <button 
                      x-on:click.stop="editId = editId === {{ $empresa->id }} ? null : {{ $empresa->id }}; isFormOpen = true; isRegister = false"
                      class="text-blue-600 hover:text-blue-800"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                  </td>
                </tr>
                <!-- Edit Form (Mobile Only) -->
                <tr x-show="isFormOpen && editId === {{ $empresa->id }}" x-cloak class="md:hidden">
                  <td colspan="5" class="px-6 py-4">
                    <div class="bg-gray-50 rounded-lg p-6">
                      <h3 class="text-lg font-semibold text-gray-900 mb-4">Editar Empresa</h3>
                      <form method="POST" action="{{ route('moderador.updateEmpresa', $empresa) }}">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                          <!-- Nombre -->
                          <div>
                            <label for="nombre_{{ $empresa->id }}" class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input 
                              type="text" 
                              id="nombre_{{ $empresa->id }}" 
                              name="nombre" 
                              value="{{ old('nombre', $empresa->name) }}"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                              required
                            >
                          </div>
                          <!-- Correo -->
                          <div>
                            <label for="correo_{{ $empresa->id }}" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                            <input 
                              type="email" 
                              id="correo_{{ $empresa->id }}" 
                              name="correo" 
                              value="{{ old('correo', $empresa->user->email) }}"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                              required
                            >
                          </div>
                          <!-- Contraseña -->
                          <div>
                            <label for="contrasena_{{ $empresa->id }}" class="block text-sm font-medium text-gray-700">Nueva Contraseña (Opcional)</label>
                            <input 
                              type="password" 
                              id="contrasena_{{ $empresa->id }}" 
                              name="contrasena"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                              placeholder="Ingrese nueva contraseña"
                            >
                          </div>
                          <!-- Confirmar Contraseña -->
                          <div>
                            <label for="contrasena_confirmation_{{ $empresa->id }}" class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
                            <input 
                              type="password" 
                              id="contrasena_confirmation_{{ $empresa->id }}" 
                              name="contrasena_confirmation"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                              placeholder="Confirme la contraseña"
                            >
                          </div>
                          <!-- Teléfono -->
                          <div>
                            <label for="telefono_{{ $empresa->id }}" class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input 
                              type="text" 
                              id="telefono_{{ $empresa->id }}" 
                              name="telefono" 
                              value="{{ old('telefono', $empresa->telefono) }}"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                              required
                            >
                          </div>
                        </div>
                        <!-- Edit Form Actions -->
                        <div class="mt-6 flex justify-end space-x-3">
                          <button 
                            type="button"
                            x-on:click="editId = null; isFormOpen = false" 
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
                            x-on:click="if (confirm('¿Estás seguro de eliminar esta empresa?')) { $refs.deleteForm{{ $empresa->id }}.submit(); }"
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
                          >
                            <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12M10 3h4" />
                            </svg>
                          </button>
                        </div>
                      </form>
                      <!-- Delete Form -->
                      <form 
                        x-ref="deleteForm{{ $empresa->id }}"
                        method="POST" 
                        action="{{ route('moderador.deleteEmpresa', $empresa) }}" 
                        class="hidden"
                      >
                        @csrf
                        @method('DELETE')
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>

    <!-- Right Side: Form (Desktop Only) -->
    <div class="hidden md:block w-full md:w-1/2">
      <!-- Registration Form -->
      <div 
        x-show="isRegister && !editId" 
        class="bg-white rounded-lg shadow-md p-6"
      >
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Registrar Empresa</h2>
        <form method="POST" action="{{ route('moderador.registerEmpresa') }}">
          @csrf
          <div class="space-y-4">
            <!-- Nombre -->
            <div>
              <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre de la Empresa</label>
              <input 
                type="text" 
                id="nombre" 
                name="nombre" 
                value="{{ old('nombre') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                placeholder="Ingresa el nombre"
                required
              >
            </div>
            <!-- Correo -->
            <div>
              <label for="correo" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
              <input 
                type="email" 
                id="correo" 
                name="correo" 
                value="{{ old('correo') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                placeholder="Ingresa el correo"
                required
              >
            </div>
            <!-- Contraseña -->
            <div>
              <label for="contrasena" class="block text-sm font-medium text-gray-700">Contraseña</label>
              <input 
                type="password" 
                id="contrasena" 
                name="contrasena"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                placeholder="Ingresa la contraseña"
                required
              >
            </div>
            <!-- Confirmar Contraseña -->
            <div>
              <label for="contrasena_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
              <input 
                type="password" 
                id="contrasena_confirmation" 
                name="contrasena_confirmation"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                placeholder="Confirma la contraseña"
                required
              >
            </div>
            <!-- Teléfono -->
            <div>
              <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
              <input 
                type="text" 
                id="telefono" 
                name="telefono" 
                value="{{ old('telefono') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                placeholder="Ingresa el teléfono"
                required
              >
            </div>
          </div>
          <!-- Form Actions -->
          <div class="mt-6 flex justify-end space-x-3">
            <button 
              type="button"
              x-on:click="isRegister = false" 
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
          x-show="editId === {{ $empresa->id }}"
          class="bg-white rounded-lg shadow-md p-6"
        >
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Editar Empresa</h3>
          <form method="POST" action="{{ route('moderador.updateEmpresa', $empresa) }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
              <!-- Nombre -->
              <div>
                <label for="nombre_{{ $empresa->id }}" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input 
                  type="text" 
                  id="nombre_{{ $empresa->id }}" 
                  name="nombre" 
                  value="{{ old('nombre', $empresa->name) }}"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                  required
                >
              </div>
              <!-- Correo -->
              <div>
                <label for="correo_{{ $empresa->id }}" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                <input 
                  type="email" 
                  id="correo_{{ $empresa->id }}" 
                  name="correo" 
                  value="{{ old('correo', $empresa->user->email) }}"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                  required
                >
              </div>
              <!-- Contraseña -->
              <div>
                <label for="contrasena_{{ $empresa->id }}" class="block text-sm font-medium text-gray-700">Nueva Contraseña (Opcional)</label>
                <input 
                  type="password" 
                  id="contrasena_{{ $empresa->id }}" 
                  name="contrasena"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                  placeholder="Ingresa nueva contraseña"
                >
              </div>
              <!-- Confirmar Contraseña -->
              <div>
                <label for="contrasena_confirmation_{{ $empresa->id }}" class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
                <input 
                  type="password" 
                  id="contrasena_confirmation_{{ $empresa->id }}" 
                  name="contrasena_confirmation"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                  placeholder="Confirma la contraseña"
                >
              </div>
              <!-- Teléfono -->
              <div>
                <label for="telefono_{{ $empresa->id }}" class="block text-sm font-medium text-gray-700">Teléfono</label>
                <input 
                  type="text" 
                  id="telefono_{{ $empresa->id }}" 
                  name="telefono" 
                  value="{{ old('telefono', $empresa->telefono) }}"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                  required
                >
              </div>
            </div>
            <!-- Edit Form Actions -->
            <div class="mt-6 flex justify-end space-x-3">
              <button 
                type="button"
                x-on:click="editId = null" 
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
                x-on:click="if (confirm('¿Estás seguro de eliminar esta empresa?')) { $refs.deleteForm{{ $empresa->id }}.submit(); }"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
              >
                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12M10 3h4" />
                </svg>
              </button>
            </div>
          </form>
          <!-- Delete Form -->
          <form 
            x-ref="deleteForm{{ $empresa->id }}"
            method="POST" 
            action="{{ route('moderador.deleteEmpresa', $empresa) }}" 
            class="hidden"
          >
            @csrf
            @method('DELETE')
          </form>
        </div>
      @endforeach
    </div>

    <!-- Registration/Edit Form (Mobile Only) -->
    <div 
      x-show="isFormOpen" 
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0 transform -translate-y-4"
      x-transition:enter-end="opacity-100 transform translate-y-0"
      x-transition:leave="transition ease-in duration-200"
      x-transition:leave-start="opacity-100 transform translate-y-0"
      x-transition:leave-end="opacity-0 transform -translate-y-4"
      class="md:hidden bg-white rounded-lg shadow-md p-6 mt-4"
    >
      <div x-show="isRegister && !editId">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Registrar Empresa</h2>
        <form method="POST" action="{{ route('moderador.registerEmpresa') }}">
          @csrf
          <div class="space-y-4">
            <!-- Nombre -->
            <div>
              <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre de la Empresa</label>
              <input 
                type="text" 
                id="nombre" 
                name="nombre" 
                value="{{ old('nombre') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                placeholder="Ingresa el nombre"
                required
              >
            </div>
            <!-- Correo -->
            <div>
              <label for="correo" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
              <input 
                type="email" 
                id="correo" 
                name="correo" 
                value="{{ old('correo') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                placeholder="Ingresa el correo"
                required
              >
            </div>
            <!-- Contraseña -->
            <div>
              <label for="contrasena" class="block text-sm font-medium text-gray-700">Contraseña</label>
              <input 
                type="password" 
                id="contrasena" 
                name="contrasena"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                placeholder="Ingresa la contraseña"
                required
              >
            </div>
            <!-- Confirmar Contraseña -->
            <div>
              <label for="contrasena_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
              <input 
                type="password" 
                id="contrasena_confirmation" 
                name="contrasena_confirmation"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                placeholder="Confirma la contraseña"
                required
              >
            </div>
            <!-- Teléfono -->
            <div>
              <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
              <input 
                type="text" 
                id="telefono" 
                name="telefono" 
                value="{{ old('telefono') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                placeholder="Ingresa el teléfono"
                required
              >
            </div>
          </div>
          <!-- Form Actions -->
          <div class="mt-6 flex justify-end space-x-3">
            <button 
              type="button"
              x-on:click="isFormOpen = false; isRegister = false" 
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

      @foreach ($empresas as $empresa)
        <div x-show="isFormOpen && editId === {{ $empresa->id }}">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Editar Empresa</h3>
          <form method="POST" action="{{ route('moderador.updateEmpresa', $empresa) }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
              <!-- Nombre -->
              <div>
                <label for="nombre_{{ $empresa->id }}" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input 
                  type="text" 
                  id="nombre_{{ $empresa->id }}" 
                  name="nombre" 
                  value="{{ old('nombre', $empresa->name) }}"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                  required
                >
              </div>
              <!-- Correo -->
              <div>
                <label for="correo_{{ $empresa->id }}" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                <input 
                  type="email" 
                  id="correo_{{ $empresa->id }}" 
                  name="correo" 
                  value="{{ old('correo', $empresa->user->email) }}"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                  required
                >
              </div>
              <!-- Contraseña -->
              <div>
                <label for="contrasena_{{ $empresa->id }}" class="block text-sm font-medium text-gray-700">Nueva Contraseña (Opcional)</label>
                <input 
                  type="password" 
                  id="contrasena_{{ $empresa->id }}" 
                  name="contrasena"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                  placeholder="Ingresa nueva contraseña"
                >
              </div>
              <!-- Confirmar Contraseña -->
              <div>
                <label for="contrasena_confirmation_{{ $empresa->id }}" class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
                <input 
                  type="password" 
                  id="contrasena_confirmation_{{ $empresa->id }}" 
                  name="contrasena_confirmation"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                  placeholder="Confirma la contraseña"
                >
              </div>
              <!-- Teléfono -->
              <div>
                <label for="telefono_{{ $empresa->id }}" class="block text-sm font-medium text-gray-700">Teléfono</label>
                <input 
                  type="text" 
                  id="telefono_{{ $empresa->id }}" 
                  name="telefono" 
                  value="{{ old('telefono', $empresa->telefono) }}"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                  required
                >
              </div>
            </div>
            <!-- Edit Form Actions -->
            <div class="mt-6 flex justify-end space-x-3">
              <button 
                type="button"
                x-on:click="editId = null; isFormOpen = false" 
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
                x-on:click="if (confirm('¿Estás seguro de eliminar esta empresa?')) { $refs.deleteForm{{ $empresa->id }}.submit(); }"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
              >
                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12M10 3h4" />
                </svg>
              </button>
            </div>
          </form>
          <!-- Delete Form -->
          <form 
            x-ref="deleteForm{{ $empresa->id }}"
            method="POST" 
            action="{{ route('moderador.deleteEmpresa', $empresa) }}" 
            class="hidden"
          >
            @csrf
            @method('DELETE')
          </form>
        </div>
      @endforeach
    </div>
  </div>

  <style>
    [x-cloak] { display: none !important; }
  </style>
</div>