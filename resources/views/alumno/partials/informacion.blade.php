<!DOCTYPE html>
<html lang="en">
<head>
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

<div class="flex flex-col md:flex-row gap-6">
    <!-- Calendario de asistencia -->
    <div class="w-full md:w-1/2">
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

    <!-- Vista del plan -->
    <div class="w-full md:w-1/2">
        @include('moderador.partials.plan_vista', ['alumno' => $alumno])
    </div>
</div>

<style>
    [x-cloak] { display: none; }
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
</script>