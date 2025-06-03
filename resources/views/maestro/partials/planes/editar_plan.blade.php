@php
    use Illuminate\Support\Facades\Auth;
    use App\Models\Maestro;
    use App\Models\Plan;

    $maestro = Maestro::where('id_user', Auth::id())->first();
    $planes = $maestro ? Plan::where('id_maestro', $maestro->id)->with(['temas.subtemas'])->get() : collect();
@endphp

<div x-data="{
        planes: @js($planes->map(fn($plan) => [
            'id' => $plan->id,
            'nombre' => $plan->nombre,
            'fecha' => $plan->created_at->format('d/m/Y'),
            'temas' => $plan->temas->map(fn($tema) => [
                'id' => $tema->id,
                'nombre' => $tema->nombre,
                'descripcion' => $tema->descripcion,
                'subtemas' => $tema->subtemas->map(fn($s) => [
                    'id' => $s->id,
                    'nombre' => $s->nombre,
                    'descripcion' => $s->descripcion,
                    'rutas' => $s->rutas,
                ]),
            ]),
        ])),
        vista: 'listado', // listado | editar
        planSeleccionado: null,
    }" class="w-full">

    <!-- Grid de planes -->
    <template x-if="vista === 'listado'">
        <div>
            <div class="mb-4 font-bold text-lg text-gray-800">Tus Planes</div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <template x-for="plan in planes" :key="plan.id">
                    <div
                        class="bg-white border border-gray-200 rounded-xl shadow hover:shadow-lg flex flex-col items-center p-6 cursor-pointer transition group relative"
                        @click="planSeleccionado = JSON.parse(JSON.stringify(plan)); vista = 'editar'">
                        <span class="text-blue-900 font-semibold text-xl text-center mb-2" x-text="plan.nombre"></span>
                        <span class="text-gray-400 text-xs">Creado el <span x-text="plan.fecha"></span></span>
                        <div class="absolute top-2 right-2 bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs opacity-0 group-hover:opacity-100 transition">
                            Editar
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    <!-- Pantalla de edición de plan -->
    <template x-if="vista === 'editar'">
        <div class="max-w-2xl mx-auto mt-8 bg-white p-8 rounded-xl shadow relative animate-fade-in">
            <button @click="vista = 'listado'; planSeleccionado = null"
                class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-2xl transition">
                &times;
            </button>
            <div class="mb-6 p-4 bg-yellow-100 border-l-4 border-yellow-400 text-yellow-800 rounded">
                <b>Nota:</b> Aquí puedes agregar, editar o eliminar temas y subtemas.<br>
                Recuerda guardar los cambios al terminar.
            </div>
            <!-- Nombre del plan -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Nombre del Plan:</label>
                <input type="text" x-model="planSeleccionado.nombre"
                    class="w-full border border-gray-300 rounded p-2" placeholder="Escribe el nombre del plan" required>
            </div>
            <!-- Botón para agregar tema -->
            <button type="button"
                @click="planSeleccionado.temas.push({nombre: '', descripcion: '', subtemas: []})"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition mb-2">
                + Agregar Tema
            </button>
            <!-- Lista de temas -->
            <template x-for="(tema, tIndex) in planSeleccionado.temas" :key="tIndex">
                <div class="border p-4 rounded bg-gray-50 mt-4 shadow-sm">
                    <div class="mb-2 flex flex-col md:flex-row gap-2 items-center">
                        <input type="text" x-model="tema.nombre" class="w-full md:w-1/3 border border-gray-300 rounded p-2"
                               placeholder="Nombre del tema">
                        <input type="text" x-model="tema.descripcion" class="w-full md:w-2/3 border border-gray-300 rounded p-2"
                               placeholder="Descripción del tema">
                        <button type="button"
                            @click="planSeleccionado.temas.splice(tIndex, 1)"
                            class="ml-2 text-red-600 hover:underline">Eliminar Tema</button>
                    </div>
                    <!-- Subtemas -->
                    <div class="pl-4 md:pl-8 border-l-4 border-blue-300 bg-blue-50 py-2 rounded">
                        <div class="font-semibold text-blue-700 text-sm mb-1">Subtemas de este tema</div>
                        <template x-for="(subtema, sIndex) in tema.subtemas" :key="sIndex">
                            <div class="mb-4">
                                <div class="flex flex-col md:flex-row gap-2 items-center mb-1">
                                    <input type="text" x-model="subtema.nombre"
                                           class="w-full md:w-1/3 border border-gray-300 rounded p-2 text-xs"
                                           placeholder="Nombre del subtema">
                                    <input type="text" x-model="subtema.descripcion"
                                           class="w-full md:w-2/3 border border-gray-300 rounded p-2 text-xs"
                                           placeholder="Descripción del subtema">
                                    <button type="button"
                                        @click="tema.subtemas.splice(sIndex, 1)"
                                        class="ml-2 text-red-500 hover:underline text-xs">Eliminar</button>
                                </div>
                                <!-- Archivos existentes -->
                                <template x-if="subtema.rutas && subtema.rutas.length">
                                    <ul class="list-disc list-inside text-xs text-gray-600">
                                        <template x-for="ruta in subtema.rutas" :key="ruta">
                                            <li>
                                                <a :href="'{{ asset('') }}' + ruta" target="_blank" class="text-blue-600 underline break-all"
                                                    x-text="ruta.split('/').pop()"></a>
                                            </li>
                                        </template>
                                    </ul>
                                </template>
                            </div>
                        </template>
                        <button type="button"
                                @click="tema.subtemas.push({nombre: '', descripcion: '', rutas: []})"
                                class="mt-1 px-3 py-1 bg-blue-200 text-blue-700 rounded hover:bg-blue-300 transition text-xs">
                            + Agregar Subtema
                        </button>
                    </div>
                </div>
            </template>
            <!-- Guardar (solo visual, no funcional) -->
            <button type="button"
                class="w-full mt-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded font-bold transition opacity-60 cursor-not-allowed"
                disabled>
                Guardar Cambios (demo visual)
            </button>
        </div>
    </template>
</div>

<style>
@keyframes fade-in { from {opacity:0;transform:scale(.97);} to {opacity:1;transform:scale(1);} }
.animate-fade-in { animation: fade-in .18s cubic-bezier(.4,0,.2,1); }
</style>
