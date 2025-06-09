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
            <div x-data="{open:false}" class="bg-white rounded-lg shadow p-4">
                <button @click="open=!open" class="w-full text-left">
                    <h3 class="text-lg font-semibold text-blue-800">{{ $tema->nombre }}</h3>
                    @if($tema->descripcion)
                        <p class="text-sm text-gray-600">{{ Str::limit($tema->descripcion,100) }}</p>
                    @endif
                </button>
                <div x-show="open" x-cloak class="mt-3 space-y-2">
                    @if($tema->descripcion)
                        <p class="text-sm text-gray-600 whitespace-pre-line">{{ $tema->descripcion }}</p>
                    @endif
                    @foreach($tema->subtemas as $subtema)
                        <a href="{{ route('alumno.subtema', $subtema->id) }}" class="block border rounded p-2 hover:bg-gray-50">
                            <h4 class="font-medium text-blue-700">{{ $subtema->nombre }}</h4>
                            @if($subtema->descripcion)
                                <p class="text-xs text-gray-600">{{ Str::limit($subtema->descripcion,80) }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endif