<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Plantel;
use App\Models\Moderador;
use App\Models\Empresa;
use App\Models\Institucion;
use App\Models\Especialidad;
use App\Models\Maestro;
use App\Models\Alumno;

class PlantelesDemoSeeder extends Seeder
{
    public function run(): void
    {
        $domain = '@gmail.com';
        $telefono = '86622222222';

        $empresaCount = 1;
        $institucionCount = 1;
        $maestroCount = 1;
        $especialidadCount = 1;
        $alumnoCount = 1;
        $moderadorCount = 1;

        // Fechas posibles de inicio
        $fechasInicio = [
            '2025-06-01',
            '2025-07-01',
            '2025-08-01',
            '2025-09-01',
            '2025-10-01',
            '2025-11-01',
            '2025-12-01',
        ];

        // Arreglo de días posibles para los alumnos
        $diasOpciones = [
            // L-V
            ['lunes' => true, 'martes' => true, 'miercoles' => true, 'jueves' => true, 'viernes' => true, 'sabado' => false, 'domingo' => false],
            // L-J
            ['lunes' => true, 'martes' => true, 'miercoles' => true, 'jueves' => true, 'viernes' => false, 'sabado' => false, 'domingo' => false],
            // M-S
            ['lunes' => false, 'martes' => true, 'miercoles' => true, 'jueves' => true, 'viernes' => true, 'sabado' => true, 'domingo' => false],
        ];

        $diasIndex = 0;
        $fechaIndex = 0;

        for ($p = 1; $p <= 2; $p++) {
            $plantel = Plantel::create(['nombre' => "Plantel $p"]);

            // Moderador
            $modUserId = DB::table('users')->insertGetId([
                'email'             => 'mod' . $moderadorCount . $domain,
                'password'          => Hash::make('123mod'),
                'type'              => 'moderador',
                'email_verified_at' => Carbon::now(),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]);

            Moderador::create([
                'id_user'    => $modUserId,
                'id_plantel' => $plantel->id,
                'name'       => "Moderador $moderadorCount",
            ]);
            $moderadorCount++;

            // Empresas (dos por plantel)
            $empresas = [];
            for ($e = 1; $e <= 2; $e++) {
                $empUserId = DB::table('users')->insertGetId([
                    'email'             => 'emp' . $empresaCount . $domain,
                    'password'          => Hash::make('123emp'),
                    'type'              => 'empresa',
                    'email_verified_at' => Carbon::now(),
                    'created_at'        => Carbon::now(),
                    'updated_at'        => Carbon::now(),
                ]);

                $empresas[] = Empresa::create([
                    'id_user'    => $empUserId,
                    'id_plantel' => $plantel->id,
                    'name'       => "Empresa $empresaCount",
                    'telefono'   => $telefono,
                ]);
                $empresaCount++;
            }

            // Instituciones (dos por plantel)
            for ($i = 1; $i <= 2; $i++) {
                $insUserId = DB::table('users')->insertGetId([
                    'email'             => 'ins' . $institucionCount . $domain,
                    'password'          => Hash::make('123ins'),
                    'type'              => 'institucion',
                    'email_verified_at' => Carbon::now(),
                    'created_at'        => Carbon::now(),
                    'updated_at'        => Carbon::now(),
                ]);

                $institucion = Institucion::create([
                    'id_user'    => $insUserId,
                    'id_plantel' => $plantel->id,
                    'name'       => "Institucion $institucionCount",
                ]);

                // Especialidades (dos por institucion)
                $especialidades = [];
                for ($esp = 1; $esp <= 2; $esp++) {
                    $especialidades[] = Especialidad::create([
                        'name'           => "Especialidad $especialidadCount",
                        'id_plantel'     => $plantel->id,
                        'id_institucion' => $institucion->id,
                    ]);
                    $especialidadCount++;
                }

                // Maestros (dos por institucion)
                for ($m = 1; $m <= 2; $m++) {
                    $maeUserId = DB::table('users')->insertGetId([
                        'email'             => 'mae' . $maestroCount . $domain,
                        'password'          => Hash::make('123mae'),
                        'type'              => 'maestro',
                        'email_verified_at' => Carbon::now(),
                        'created_at'        => Carbon::now(),
                        'updated_at'        => Carbon::now(),
                    ]);

                    $maestro = Maestro::create([
                        'id_user'       => $maeUserId,
                        'id_institucion'=> $institucion->id,
                        'id_plantel'    => $plantel->id,
                        'name'          => "Maestro $maestroCount",
                        'telefono'      => $telefono,
                    ]);

                    $especialidadId = $especialidades[$m - 1]->id;
                    $empresaId     = $empresas[$m - 1]->id;

                    // Alumnos (dos por maestro)
                    for ($a = 1; $a <= 2; $a++) {
                        $aluUserId = DB::table('users')->insertGetId([
                            'email'             => 'alu' . $alumnoCount . $domain,
                            'password'          => Hash::make('123alu'),
                            'type'              => 'alumno',
                            'email_verified_at' => Carbon::now(),
                            'created_at'        => Carbon::now(),
                            'updated_at'        => Carbon::now(),
                        ]);

                        // Selección intercalada de fecha y días
                        $fecha_inicio = $fechasInicio[$fechaIndex % count($fechasInicio)];
                        $fecha_termino = Carbon::parse($fecha_inicio)->addMonths(6)->toDateString();
                        $dias = $diasOpciones[$diasIndex % count($diasOpciones)];
                        $diasIndex++;
                        $fechaIndex++;

                        Alumno::create([
                            'id_user'            => $aluUserId,
                            'id_plantel'         => $plantel->id,
                            'id_especialidad'    => $especialidadId,
                            'name'               => "Alumno $alumnoCount",
                            'telefono'           => $telefono,
                            'telefono_emergencia'=> $telefono,
                            'fecha_inicio'       => $fecha_inicio,
                            'fecha_termino'      => $fecha_termino,
                            'id_empresa'         => $empresaId,
                            'id_maestro'         => $maestro->id,
                            'id_institucion'     => $institucion->id,
                            'id_plan'            => null,
                            'lunes'              => $dias['lunes'],
                            'martes'             => $dias['martes'],
                            'miercoles'          => $dias['miercoles'],
                            'jueves'             => $dias['jueves'],
                            'viernes'            => $dias['viernes'],
                            'sabado'             => $dias['sabado'],
                            'domingo'            => $dias['domingo'],
                            'created_at'         => Carbon::now(),
                            'updated_at'         => Carbon::now(),
                        ]);
                        $alumnoCount++;
                    }
                    $maestroCount++;
                }
                $institucionCount++;
            }
        }
    }
}
