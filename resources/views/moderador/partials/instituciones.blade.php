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

<div class="p-4 max-w-full mx-auto" x-data="{ 
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
        <div class="flex flex-wrap gap-2 border-b border-gray-200 overflow-x-auto">
            <a 
                href="{{ route('moderador.inicio', ['tab' => 'instituciones']) }}"
                x-on:click="activeTab = 'instituciones'; isFormOpenMaestro = false; isFormOpenEspecialidad = false; editIdMaestro = null; editIdEspecialidad = null" 
                :class="{ 'border-blue-500 text-blue-600': activeTab === 'instituciones', 'text-gray-600': activeTab !== 'instituciones' }" 
                class="px-4 py-2 font-medium border-b-2 flex-shrink-0 transition-colors duration-200"
            >
                Instituciones
            </a>
            <a 
                href="{{ route('moderador.inicio', ['tab' => 'maestros']) }}"
                x-on:click="activeTab = 'maestros'; isFormOpenInstitucion = false; isFormOpenEspecialidad = false; editIdInstitucion = null; editIdEspecialidad = null" 
                :class="{ 'border-blue-500 text-blue-600': activeTab === 'maestros', 'text-gray-600': activeTab !== 'maestros' }" 
                class="px-4 py-2 font-medium border-b-2 flex-shrink-0 transition-colors duration-200"
            >
                Maestros
            </a>
            <a 
                href="{{ route('moderador.inicio', ['tab' => 'especialidades']) }}"
                x-on:click="activeTab = 'especialidades'; isFormOpenInstitucion = false; isFormOpenMaestro = false; editIdInstitucion = null; editIdMaestro = null" 
                :class="{ 'border-blue-500 text-blue-600': activeTab === 'especialidades', 'text-gray-600': activeTab !== 'especialidades' }" 
                class="px-4 py-2 font-medium border-b-2 flex-shrink-0 transition-colors duration-200"
            >
                Especialidades
            </a>
        </div>
    </div>

    <!-- Sub-Partial Includes -->
    <div class="overflow-x-auto">
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
    </div>

    <style>
        [x-cloak] { display: none !important; }

        /* Ensure tabs are scrollable on small screens */
        .flex-wrap {
            flex-wrap: wrap;
        }

        .overflow-x-auto {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
        }

        /* Make sure the content area doesn't overflow */
        .max-w-full {
            max-width: 100%;
        }

        /* Responsive table adjustments */
        table {
            width: 100%;
            min-width: 600px; /* Ensures table content doesn't shrink too much */
        }

        /* Adjust padding and font sizes for smaller screens */
        @media (max-width: 640px) {
            .px-4 {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .py-2 {
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }

            .font-medium {
                font-size: 0.875rem; /* Smaller font size for tabs on mobile */
            }

            .p-4 {
                padding: 1rem; /* Reduce padding on mobile */
            }
        }
    </style>
</div>