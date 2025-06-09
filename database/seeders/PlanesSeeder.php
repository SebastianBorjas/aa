<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Maestro;
use App\Models\Plan;
use App\Models\Tema;
use App\Models\Subtema;
use App\Models\Alumno;

class PlanesSeeder extends Seeder
{
    public function run(): void
    {
        $description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin vel turpis a justo mollis consectetur. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae;';

        $maestros = Maestro::all();
        foreach ($maestros as $maestro) {
            // Crear Plan 1
            $plan1 = Plan::create([
                'id_maestro' => $maestro->id,
                'nombre'     => "Plan 1, Maestro {$maestro->id}",
            ]);

            for ($t = 1; $t <= 4; $t++) {
                $tema = Tema::create([
                    'id_plan'     => $plan1->id,
                    'nombre'      => "Tema {$t}",
                    'descripcion' => $description,
                ]);

                for ($s = 1; $s <= 4; $s++) {
                    Subtema::create([
                        'id_tema'     => $tema->id,
                        'nombre'      => "Subtema {$s}",
                        'descripcion' => $description,
                        'rutas'       => null,
                    ]);
                }
            }

            // Crear Plan 2
            $plan2 = Plan::create([
                'id_maestro' => $maestro->id,
                'nombre'     => "Plan 2, Maestro {$maestro->id}",
            ]);

            for ($t = 1; $t <= 8; $t++) {
                $tema = Tema::create([
                    'id_plan'     => $plan2->id,
                    'nombre'      => "Tema {$t}",
                    'descripcion' => $description,
                ]);

                for ($s = 1; $s <= 2; $s++) {
                    Subtema::create([
                        'id_tema'     => $tema->id,
                        'nombre'      => "Subtema {$s}",
                        'descripcion' => $description,
                        'rutas'       => null,
                    ]);
                }
            }

            // Asignar planes a alumnos alternando
            $alumnos = Alumno::where('id_maestro', $maestro->id)->get();
            $planIds = [$plan1->id, $plan2->id];
            $i = 0;
            foreach ($alumnos as $alumno) {
                $alumno->id_plan = $planIds[$i % 2];
                $alumno->save();
                $i++;
            }
        }
    }
}