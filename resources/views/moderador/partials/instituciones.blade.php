@php
    use App\Models\Institucion;
    use App\Models\Maestro;
    use App\Models\Especialidad;
    use App\Models\Moderador;
    use Illuminate\Support\Facades\Auth;

    // Get the moderator's id_plantel
    $moderador = Moderador::where('id_user', Auth::id())->first();
    // Fetch data for the moderator's id_plantel
    $instituciones = $moderador ? Institucion::where('id_plantel', $moderador->id_plantel)->get() : collect();
    $maestros = $moderador ? Maestro::where('id_plantel', $moderador->id_plantel)->with('user','institucion')->get() : collect();
    $especialidades = $moderador ? Especialidad::where('id_plantel', $moderador->id_plantel)->with('institucion')->get() : collect();
@endphp

<div class="p-4" x-data="{ 
    activeTab: '{{ $subtab ?? 'instituciones' }}', 
    isFormOpenInstitucion: {{ $errors->any() && old('section') == 'institucion' ? 'true' : 'false' }}, 
    isFormOpenMaestro: {{ $errors->any() && old('section') == 'maestro' ? 'true' : 'false' }}, 
    isFormOpenEspecialidad: {{ $errors->any() && old('section') == 'especialidad' ? 'true' : 'false' }}, 
    editIdInstitucion: null, 
    editIdMaestro: null, 
    editIdEspecialidad: null 
}">
    <!-- Tabs -->
    <div class="mb-4">
        <div class="flex space-x-4 border-b">
            <a 
                href="{{ route('moderador.inicio', ['tab' => 'instituciones']) }}"
                x-on:click="activeTab = 'instituciones'; isFormOpenMaestro = false; isFormOpenEspecialidad = false; editIdMaestro = null; editIdEspecialidad = null" 
                :class="{ 'border-blue-500 text-blue-600': activeTab === 'instituciones', 'text-gray-600': activeTab !== 'instituciones' }" 
                class="px-4 py-2 font-medium border-b-2"
            >
                Instituciones
            </a>
            <a 
                href="{{ route('moderador.inicio', ['tab' => 'maestros']) }}"
                x-on:click="activeTab = 'maestros'; isFormOpenInstitucion = false; isFormOpenEspecialidad = false; editIdInstitucion = null; editIdEspecialidad = null" 
                :class="{ 'border-blue-500 text-blue-600': activeTab === 'maestros', 'text-gray-600': activeTab !== 'maestros' }" 
                class="px-4 py-2 font-medium border-b-2"
            >
                Maestros
            </a>
            <a 
                href="{{ route('moderador.inicio', ['tab' => 'especialidades']) }}"
                x-on:click="activeTab = 'especialidades'; isFormOpenInstitucion = false; isFormOpenMaestro = false; editIdInstitucion = null; editIdMaestro = null" 
                :class="{ 'border-blue-500 text-blue-600': activeTab === 'especialidades', 'text-gray-600': activeTab !== 'especialidades' }" 
                class="px-4 py-2 font-medium border-b-2"
            >
                Especialidades
            </a>
        </div>
    </div>

    <!-- Sub-Partial Includes -->
    <div x-show="activeTab === 'instituciones'" x-transition>
        @include('moderador.partials.instituciones.instituciones_table', [
            'instituciones' => $instituciones,
            'isFormOpenInstitucion' => 'isFormOpenInstitucion',
            'editIdInstitucion' => 'editIdInstitucion'
        ])
    </div>
    <div x-show="activeTab === 'maestros'" x-transition>
        @include('moderador.partials.instituciones.maestros_table', [
            'maestros' => $maestros,
            'instituciones' => $instituciones,
            'isFormOpenMaestro' => 'isFormOpenMaestro',
            'editIdMaestro' => 'editIdMaestro'
        ])
    </div>
    <div x-show="activeTab === 'especialidades'" x-transition>
        @include('moderador.partials.instituciones.especialidades_table', [
            'especialidades' => $especialidades,
            'instituciones' => $instituciones,
            'isFormOpenEspecialidad' => 'isFormOpenEspecialidad',
            'editIdEspecialidad' => 'editIdEspecialidad'
        ])
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</div>