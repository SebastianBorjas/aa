@php
    use Illuminate\Support\Str;
    $plan = $alumno?->plan;
@endphp

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-2 mb-4 rounded shadow text-center">{{ session('success') }}</div>
@elseif(session('error'))
    <div class="bg-red-100 text-red-800 p-2 mb-4 rounded shadow text-center">{{ session('error') }}</div>
@endif

@if(!$plan)
    <div class="text-center text-gray-500">
        Solicita que se te asigne un plan.
    </div>
@else
    <h2 class="text-center text-2xl font-bold text-blue-900 mb-6">{{ $plan->nombre }}</h2>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($plan->temas as $tema)
            @php $rechazado = $tema->subtemas->flatMap->entregas->where('estado', 'rechazado')->isNotEmpty(); @endphp
            <a href="{{ route('alumno.tema', $tema->id) }}" class="relative block bg-gray-50 border border-gray-300 rounded-lg p-4 shadow-sm hover:shadow-md transition-transform transform hover:-translate-y-1 group">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="font-semibold text-gray-900 group-hover:text-blue-800">{{ $tema->nombre }}</div>
                        @if($tema->descripcion)
                            <div class="text-xs text-gray-500 mt-1">{{ Str::limit($tema->descripcion, 80) }}</div>
                        @endif
                    </div>
                    <svg class="w-5 h-5 text-blue-700 group-hover:text-blue-900 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
                @if($rechazado)
                    <span class="absolute top-2 right-2 inline-block w-2 h-2 bg-red-500 rounded-full"></span>
                @endif
            </a>
        @endforeach
    </div>
@endif