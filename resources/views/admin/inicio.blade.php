@extends('layouts.base')

@push('vite')
  @vite('resources/css/app.css')
  <script src="https://unpkg.com/alpinejs" defer></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endpush

@section('title', 'Panel de Administración')

@section('main')
<div x-data="{ showPlantel: false, showModerador: false, expanded: null, showEditPlantel: false, showEditModerador: { id: null, name: '', email: '', id_user: null } }" class="flex-grow container mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow-lg space-y-8">
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
      <h1 class="text-2xl font-bold text-gray-800">Panel de Administración</h1>
      <div class="flex flex-col sm:flex-row gap-3">
        <button @click="showPlantel = true" class="px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-white rounded-lg font-medium transition w-full sm:w-auto">+ Registrar Plantel</button>
        <button @click="showModerador = true" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition w-full sm:w-auto">+ Registrar Moderador</button>
        <form action="{{ route('logout') }}" method="POST" class="w-full sm:w-auto">
          @csrf
          <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition w-full">Cerrar sesión</button>
        </form>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
      <table class="w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-50 text-xs uppercase font-medium text-gray-600">
          <tr>
            <th class="px-4 py-2">Nombre del Plantel</th>
            <th class="px-4 py-2 text-right">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @foreach ($planteles as $plantel)
            <tr @click="expanded === {{ $plantel->id }} ? expanded = null : expanded = {{ $plantel->id }}"
                class="cursor-pointer hover:bg-gray-50 transition">
              <td class="px-4 py-2 font-medium flex items-center gap-2">
                <span x-text="expanded === {{ $plantel->id }} ? '▼' : '➕'"></span>
                {{ $plantel->nombre }}
              </td>
              <td class="px-4 py-2 text-right">
                <button @click.stop="showEditPlantel = { id: {{ $plantel->id }}, nombre: '{{ $plantel->nombre }}' }; expanded = null" class="text-blue-500 hover:text-blue-700 mr-2">
                  <i class="fas fa-pencil-alt"></i>
                </button>
                <button @click.stop="if(confirm('¿Seguro que deseas eliminar este plantel?')) deletePlantel({{ $plantel->id }})" class="text-red-500 hover:text-red-700">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </td>
            </tr>
            <tr x-show="expanded === {{ $plantel->id }}" class="bg-gray-50">
              <td colspan="2" class="px-4 py-2 max-h-60 overflow-y-auto">
                <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="px-3 py-1 text-left text-gray-600">Nombre</th>
                      <th class="px-3 py-1 text-left text-gray-600">Correo</th>
                      <th class="px-3 py-1 text-right text-gray-600">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($plantel->moderadores as $moderador)
                      <tr class="hover:bg-gray-50">
                        <td class="px-3 py-1 text-gray-800">{{ $moderador->name }}</td>
                        <td class="px-3 py-1 text-gray-600">{{ $moderador->user->email }}</td>
                        <td class="px-3 py-1 text-right">
                          <div class="flex justify-end"> <!-- Añadimos un div para controlar la alineación en PC -->
                            <button @click.stop="showEditModerador = { id: {{ $moderador->id }}, name: '{{ $moderador->name }}', email: '{{ $moderador->user->email }}', id_user: {{ $moderador->user->id }} }; expanded = null" class="text-blue-500 hover:text-blue-700 mr-2">
                              <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button @click.stop="if(confirm('¿Seguro que deseas eliminar este moderador?')) deleteModerador({{ $moderador->id }}, {{ $moderador->user->id }})" class="text-red-500 hover:text-red-700">
                              <i class="fas fa-trash-alt"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    @endforeach
                    @if ($plantel->moderadores->isEmpty())
                      <tr><td colspan="3" class="px-4 py-2 text-center text-gray-400">Sin moderadores</td></tr>
                    @endif
                  </tbody>
                </table>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- MODAL PLANTEL -->
  <div x-show="showPlantel" @click.outside="showPlantel = false" class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
    <div @click.stop class="bg-white p-6 rounded-xl w-full max-w-md space-y-4">
      <h2 class="text-lg font-bold text-gray-800">Registrar Plantel</h2>
      <input type="text" id="nombre_plantel" placeholder="Nombre del plantel" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
      <div class="flex justify-end gap-2">
        <button @click="showPlantel = false" class="px-4 py-2 text-gray-600 hover:underline">Cancelar</button>
        <button @click="registrarPlantel" class="px-4 py-2 bg-yellow-400 text-gray-800 rounded hover:bg-yellow-500">Guardar</button>
      </div>
    </div>
  </div>

  <!-- MODAL EDITAR PLANTEL -->
  <div x-show="showEditPlantel" @click.outside="showEditPlantel = false" class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
    <div @click.stop class="bg-white p-6 rounded-xl w-full max-w-md space-y-4">
      <h2 class="text-lg font-bold text-gray-800">Editar Plantel</h2>
      <input x-model="showEditPlantel.nombre" type="text" id="edit_nombre_plantel" placeholder="Nombre del plantel" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
      <div class="flex justify-end gap-2">
        <button @click="showEditPlantel = false" class="px-4 py-2 text-gray-600 hover:underline">Cancelar</button>
        <button @click="updatePlantel(showEditPlantel.id)" class="px-4 py-2 bg-yellow-400 text-gray-800 rounded hover:bg-yellow-500">Guardar</button>
      </div>
    </div>
  </div>

  <!-- MODAL MODERADOR -->
  <div x-show="showModerador" @click.outside="showModerador = false" class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
    <div @click.stop class="bg-white p-6 rounded-xl w-full max-w-md space-y-4">
      <h2 class="text-lg font-bold text-gray-800">Registrar Moderador</h2>
      <input type="text" id="name_mod" placeholder="Nombre" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
      <input type="email" id="email_mod" placeholder="Correo" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
      <input type="password" id="pass_mod" placeholder="Contraseña" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
      <select id="plantel_mod" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
        @foreach ($plantelesSelect as $plantel)
          <option value="{{ $plantel->id }}">{{ $plantel->nombre }}</option>
        @endforeach
      </select>
      <div class="flex justify-end gap-2">
        <button @click="showModerador = false" class="px-4 py-2 text-gray-600 hover:underline">Cancelar</button>
        <button @click="registrarModerador" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Guardar</button>
      </div>
    </div>
  </div>

  <!-- MODAL EDITAR MODERADOR -->
  <div x-show="showEditModerador.id" @click.outside="showEditModerador = { id: null, name: '', email: '', id_user: null }" class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
    <div @click.stop class="bg-white p-6 rounded-xl w-full max-w-md space-y-4">
      <h2 class="text-lg font-bold text-gray-800">Editar Moderador</h2>
      <input x-model="showEditModerador.name" type="text" id="edit_name_mod" placeholder="Nombre" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
      <input x-model="showEditModerador.email" type="email" id="edit_email_mod" placeholder="Correo" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
      <input type="password" id="edit_pass_mod" placeholder="Contraseña (dejar vacío para no cambiar)" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
      <div class="flex justify-end gap-2">
        <button @click="showEditModerador = { id: null, name: '', email: '', id_user: null }" class="px-4 py-2 text-gray-600 hover:underline">Cancelar</button>
        <button @click="updateModerador(showEditModerador.id, showEditModerador.id_user)" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Guardar</button>
      </div>
    </div>
  </div>
</div>

<script>
  async function registrarPlantel() {
    const formData = new FormData();
    formData.append('nombre', document.getElementById('nombre_plantel').value);

    const res = await fetch('{{ route('admin.registrar_plantel') }}', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: formData
    });

    if (res.ok) location.reload();
    else {
      const error = await res.json();
      alert('Error al registrar plantel: ' + (error.message || 'Error desconocido'));
    }
  }

  async function registrarModerador() {
    const formData = new FormData();
    formData.append('name', document.getElementById('name_mod').value);
    formData.append('email', document.getElementById('email_mod').value);
    formData.append('password', document.getElementById('pass_mod').value);
    formData.append('id_plantel', document.getElementById('plantel_mod').value);

    const res = await fetch('{{ route('admin.registrar_moderador') }}', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: formData
    });

    if (res.ok) location.reload();
    else {
      const error = await res.json();
      alert('Error al registrar moderador: ' + (error.message || 'Error desconocido'));
    }
  }

  async function deletePlantel(id) {
    const res = await fetch(`{{ route('admin.eliminar_plantel', ['id' => ':id']) }}`.replace(':id', id), {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });

    if (res.ok) location.reload();
    else {
      const error = await res.json();
      alert('Error al eliminar plantel: ' + (error.message || 'Error desconocido'));
    }
  }

  async function updatePlantel(id) {
    const data = {
      nombre: document.getElementById('edit_nombre_plantel').value,
      _method: 'PUT'
    };

    const res = await fetch(`{{ route('admin.actualizar_plantel', ['id' => ':id']) }}`.replace(':id', id), {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    });

    if (res.ok) location.reload();
    else {
      const error = await res.json();
      alert('Error al actualizar plantel: ' + (error.message || JSON.stringify(error)));
    }
  }

  async function deleteModerador(moderadorId, userId) {
    const res = await fetch(`{{ route('admin.eliminar_moderador', ['id' => ':id']) }}`.replace(':id', moderadorId), {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-User-ID': userId }
    });

    if (res.ok) location.reload();
    else {
      const error = await res.json();
      alert('Error al eliminar moderador: ' + (error.message || 'Error desconocido'));
    }
  }

  async function updateModerador(moderadorId, userId) {
    const name = document.getElementById('edit_name_mod').value;
    const email = document.getElementById('edit_email_mod').value;
    const passwordInput = document.getElementById('edit_pass_mod').value;
    const password = passwordInput || undefined;

    const data = {
      name: name,
      email: email,
      password: password,
      _method: 'PUT'
    };

    console.log('Datos enviados:', data);
    console.log('User ID:', userId);

    const res = await fetch(`{{ route('admin.actualizar_moderador', ['id' => ':id']) }}`.replace(':id', moderadorId), {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Content-Type': 'application/json',
        'X-User-ID': userId.toString()
      },
      body: JSON.stringify(data)
    });

    if (res.ok) {
      location.reload();
    } else {
      const errorText = await res.text();
      try {
        const error = JSON.parse(errorText);
        alert('Error al actualizar moderador: ' + (error.message || JSON.stringify(error)));
      } catch (e) {
        alert('Error al actualizar moderador (respuesta no JSON): ' + errorText);
      }
    }
  }
</script>
@endsection