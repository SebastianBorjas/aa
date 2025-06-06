@php
    use App\Models\Empresa;
    use App\Models\Alumno;
    use Illuminate\Support\Facades\Auth;

    $empresa = Empresa::where('id_user', Auth::id())->first();
    $alumnos = $empresa
        ? Alumno::where('id_empresa', $empresa->id)
            ->with([
                'user',
                'empresa',
                'institucion',
                'maestro',
                'especialidad',
                'listas',
                'plan.temas.subtemas',
                'entregas',
            ])->get()
        : collect();
@endphp

<div class="max-w-7xl mx-auto p-4" x-data="{ editIdAlumno: null, showPlanId: null }">
    <div class="flex flex-col lg:flex-row lg:space-x-6 space-y-6 lg:space-y-0">
        <!-- Left Side: Table -->
        <div class="w-full lg:w-2/3 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Lista de Alumnos</h2>
            @if ($alumnos->isEmpty())
                <p class="text-gray-600">No hay alumnos registrados.</p>
            @else
                <div class="overflow-x-auto max-w-full">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-700 text-white">
                            <tr>
                                <th class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider">Nombre</th>
                                <th class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider">Correo</th>
                                <th class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider">Institución</th>
                                <th class="px-2 py-3 text-left text-xs font-bold uppercase tracking-wider">Especialidad</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-300">
                            @foreach($alumnos as $alumno)
                                <tr
                                    x-on:click="editIdAlumno = editIdAlumno === {{ $alumno->id }} ? null : {{ $alumno->id }}; showPlanId = null"
                                    class="cursor-pointer hover:bg-gray-200 transition"
                                    :class="{ 'bg-gray-200': editIdAlumno === {{ $alumno->id }} }">
                                    <td class="px-2 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $alumno->name }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">{{ $alumno->user->email }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">{{ $alumno->institucion->name ?? 'N/A' }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">{{ $alumno->especialidad->name ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Right Side: Details -->
        <div class="w-full lg:w-1/3 space-y-6">
            @foreach($alumnos as $alumno)
                @php
                    $attendanceData = $alumno->listas->map(fn($l) => [
                        'fecha' => $l->fecha->format('Y-m-d'),
                        'estado' => $l->estado,
                    ]);
                    $diasActivos = [];
                    if ($alumno->domingo) $diasActivos[] = 0;
                    if ($alumno->lunes) $diasActivos[] = 1;
                    if ($alumno->martes) $diasActivos[] = 2;
                    if ($alumno->miercoles) $diasActivos[] = 3;
                    if ($alumno->jueves) $diasActivos[] = 4;
                    if ($alumno->viernes) $diasActivos[] = 5;
                    if ($alumno->sabado) $diasActivos[] = 6;
                @endphp
                <div
                    x-show="editIdAlumno === {{ $alumno->id }} && showPlanId !== {{ $alumno->id }}"
                    x-cloak
                    class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Alumno</h3>
                    <div class="space-y-2">
                        <p><strong>Correo:</strong> {{ $alumno->user->email }}</p>
                        <p><strong>Nombre:</strong> {{ $alumno->name }}</p>
                        <p><strong>Teléfono:</strong> {{ $alumno->telefono }}</p>
                        <p><strong>Teléfono Emergencia:</strong> {{ $alumno->telefono_emergencia }}</p>
                        <p><strong>Institución:</strong> {{ $alumno->institucion->name ?? 'N/A' }}</p>
                        <p><strong>Especialidad:</strong> {{ $alumno->especialidad->name ?? 'N/A' }}</p>
                    </div>

                    <div class="mt-6">
                        <div class="bg-gray-100 p-4 rounded"
                             x-data='calendar(
                                @json($attendanceData),
                                "{{ $alumno->fecha_inicio->format('Y-m-d') }}",
                                "{{ $alumno->fecha_termino->format('Y-m-d') }}",
                                @json($diasActivos)
                             )'
                             x-init="init()">
                            <div class="flex items-center justify-between mb-2">
                                <button type="button" @click="prevMonth" class="text-sm px-2 py-1 bg-gray-200 rounded">&#8249;</button>
                                <div class="text-sm font-semibold" x-text="monthNames[currentMonth] + ' ' + currentYear"></div>
                                <button type="button" @click="nextMonth" class="text-sm px-2 py-1 bg-gray-200 rounded">&#8250;</button>
                            </div>
                            <div class="grid grid-cols-7 text-xs font-semibold text-center">
                                <template x-for="day in dayNames" :key="day">
                                    <div class="py-1" x-text="day"></div>
                                </template>
                            </div>
                            <div class="grid grid-cols-7 text-center text-sm">
                                <template x-for="blank in blanks" :key="'b' + blank">
                                    <div></div>
                                </template>
                                <template x-for="date in dates" :key="date.day">
                                    <div class="py-1 flex justify-center" :class="{ 'opacity-50': date.disabled }">
                                        <div class="w-6 h-6 flex items-center justify-center rounded-full"
                                             :class="{
                                                 'bg-green-600 text-white': date.estado === 'asistencia',
                                                 'bg-red-600 text-white': date.estado === 'falta',
                                                 'bg-blue-600 text-white': date.estado === 'justificado',
                                                 'bg-gray-400 text-white': date.estado === 'no_lista'
                                             }">
                                            <span x-text="date.day"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    @php $maxDate = min($alumno->fecha_termino->format('Y-m-d'), now()->toDateString()); @endphp
                    <div class="mt-4" x-data="editAttendance(
                            @json($attendanceData),
                            '{{ $alumno->fecha_inicio->format('Y-m-d') }}',
                            '{{ $alumno->fecha_termino->format('Y-m-d') }}',
                            @json($diasActivos)
                        )" x-init="fecha='{{ now()->toDateString() }}'; updateEstado()">
                        <form method="POST" action="{{ route('empresa.guardarListaAlumno', $alumno) }}" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fecha</label>
                                <input type="date" name="fecha" x-model="fecha" @change="updateEstado()" min="{{ $alumno->fecha_inicio->format('Y-m-d') }}" max="{{ $maxDate }}" class="mt-1 block w-full rounded-md border-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Estado</label>
                                <select name="estado" x-model="estado" class="mt-1 block w-full rounded-md border-gray-300">
                                    <option value="asistencia">Asistencia</option>
                                    <option value="falta">Falta</option>
                                    <option value="justificado">Justificado</option>
                                </select>
                            </div>
                            <button type="submit" :disabled="!isValidDate(fecha)" class="px-4 py-2 bg-green-600 text-white rounded disabled:opacity-50">Guardar lista</button>
                        </form>
                    </div>

                    @if($alumno->plan)
                        @php
                            $totalSubtemas = $alumno->plan->temas->sum(fn($t) => $t->subtemas->count());
                            $entregadas = $alumno->entregas->count();
                            $avance = $totalSubtemas ? intval($entregadas * 100 / $totalSubtemas) : 0;
                        @endphp
                        <div class="mt-4 bg-gray-50 p-4 rounded">
                            <h4 class="font-medium">Plan: {{ $alumno->plan->nombre }}</h4>
                            <p class="text-sm text-gray-700 mb-2">Actividades entregadas: {{ $entregadas }} / {{ $totalSubtemas }}</p>
                            <div class="w-full h-2 bg-gray-200 rounded">
                                <div class="h-2 bg-blue-600 rounded" style="width: {{ $avance }}%"></div>
                            </div>
                            <button type="button" x-on:click="showPlanId = {{ $alumno->id }}" class="mt-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Ver plan</button>
                        </div>
                    @endif
                    <div class="mt-4 text-right">
                        <button type="button" x-on:click="editIdAlumno = null" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">Cerrar</button>
                    </div>
                </div>

                <div
                    x-show="showPlanId === {{ $alumno->id }}"
                    x-cloak
                    class="bg-white rounded-lg shadow-md p-6 overflow-y-auto max-h-[80vh]">
                    @include('moderador.partials.plan_vista', ['alumno' => $alumno])
                    <div class="mt-4 text-right">
                        <button type="button" x-on:click="showPlanId = null" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cerrar</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script>
        function calendar(records = [], start = null, end = null, activeDays = []) {
            return {
                currentDate: new Date(),
                dayNames: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
                monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
                blanks: [],
                dates: [],
                records: records,
                startDate: start ? new Date(start) : null,
                endDate: end ? new Date(end) : null,
                activeDays: activeDays,
                get currentMonth() { return this.currentDate.getMonth(); },
                get currentYear() { return this.currentDate.getFullYear(); },
                init() { this.update(); },
                prevMonth() {
                    this.currentDate = new Date(this.currentYear, this.currentMonth - 1, 1);
                    this.update();
                },
                nextMonth() {
                    this.currentDate = new Date(this.currentYear, this.currentMonth + 1, 1);
                    this.update();
                },
                update() {
                    const first = new Date(this.currentYear, this.currentMonth, 1).getDay();
                    const total = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                    this.blanks = Array.from({ length: first }, (_, i) => i);

                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    this.dates = Array.from({ length: total }, (_, i) => {
                        const day = i + 1;
                        const dateObj = new Date(this.currentYear, this.currentMonth, day);
                        dateObj.setHours(0, 0, 0, 0);
                        const dateStr = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
                        const rec = this.records.find(r => r.fecha === dateStr);
                        let estado = rec ? rec.estado : null;
                        const inRange = (!this.startDate || dateObj >= this.startDate) && (!this.endDate || dateObj <= this.endDate);
                        const shouldAttend = inRange && this.activeDays.includes(dateObj.getDay());
                        const isPastOrToday = dateObj <= today;
                        if (shouldAttend && !rec && isPastOrToday) {
                            estado = 'no_lista';
                        }
                        const disabled = !inRange || dateObj > today || !shouldAttend;
                        return { day: day, estado: estado, disabled: disabled };
                    });
                }
            };
        }
        function editAttendance(records = [], start = null, end = null, activeDays = []) {
            return {
                records: records,
                fecha: '',
                estado: 'asistencia',
                startDate: start ? new Date(start) : null,
                endDate: end ? new Date(end) : null,
                activeDays: activeDays,
                isValidDate(dateStr) {
                    if (!dateStr) return false;
                    const d = new Date(dateStr);
                    d.setHours(0,0,0,0);
                    const today = new Date();
                    today.setHours(0,0,0,0);
                    if (this.startDate && d < this.startDate) return false;
                    if (this.endDate && d > this.endDate) return false;
                    if (d > today) return false;
                    if (!this.activeDays.includes(d.getDay())) return false;
                    return true;
                },
                updateEstado() {
                    if (!this.isValidDate(this.fecha)) { this.estado = 'asistencia'; return; }
                    const rec = this.records.find(r => r.fecha === this.fecha);
                    this.estado = rec ? rec.estado : 'asistencia';
                }
            };
        }
    </script>
</div>