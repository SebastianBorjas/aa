<div>
    <h2 class="text-xl font-semibold mb-4">Reporte de {{ $alumno->name }}</h2>
    <form method="POST" action="{{ route('moderador.alumno.reportePdf', $alumno) }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Fecha inicial</label>
            <input type="date" name="fecha_inicio" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Fecha final</label>
            <input type="date" name="fecha_fin" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Generar PDF</button>
    </form>
</div>