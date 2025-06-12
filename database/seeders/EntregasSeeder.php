<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Alumno;
use App\Models\Entrega;
use App\Models\Subtema;

class EntregasSeeder extends Seeder
{
    public function run(): void
    {
        $sourceFiles = [
            public_path('images/lgo.png'),
            public_path('images/lgo2.png'),
            public_path('images/lgo3.png'),
            public_path('robots.txt'),
        ];

        $alumnos = Alumno::with('plan.temas.subtemas')->get();

        foreach ($alumnos as $alumno) {
            $plan = $alumno->plan;
            if (!$plan) {
                continue;
            }

            $tema = $plan->temas->first();
            if (!$tema) {
                continue;
            }

            $subtema = $tema->subtemas->first();
            if (!$subtema) {
                continue;
            }

            $selected = collect($sourceFiles)->shuffle()->take(rand(1, 3));
            $storedPaths = [];

            foreach ($selected as $file) {
                if (!file_exists($file)) {
                    continue;
                }
                $name = Str::random(8) . '_' . basename($file);
                $path = 'entregas/' . $name;
                Storage::disk('public')->put($path, file_get_contents($file));
                $storedPaths[] = $path;
            }

            Entrega::create([
                'id_subtema' => $subtema->id,
                'id_alumno'  => $alumno->id,
                'contenido'  => 'Contenido de entrega de ejemplo',
                'rutas'      => $storedPaths,
                'estado'     => 'pen_emp',
            ]);
        }
    }
}