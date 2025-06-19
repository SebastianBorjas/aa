<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Empresa;
use App\Models\Institucion;
use App\Models\Maestro;
use App\Models\Especialidad;
use App\Models\User;
use App\Models\Moderador;
use App\Models\Lista;
use App\Models\Entrega;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Mpdf\Mpdf;

class ModController extends Controller
{
    /**
     * Display the moderator dashboard with the specified tab.
     */
    public function inicio(Request $request)
    {
        return view('moderador.inicio', [
            'tab' => $request->query('tab', 'alumnos'),
        ]);
    }

    /**
     * Register a new empresa and associated user.
     */
    public function registerEmpresa(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|unique:users,email',
            'contrasena' => 'required|string|min:6|confirmed',
            'responsable' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
        ]);

        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();

        $user = User::create([
            'email' => $validated['correo'],
            'password' => Hash::make($validated['contrasena']),
            'type' => 'empresa',
        ]);

        Empresa::create([
            'id_user' => $user->id,
            'id_plantel' => $moderador->id_plantel,
            'name' => $validated['nombre'],
            'responsable' => $validated['responsable'],
            'telefono' => $validated['telefono'],
        ]);

        return redirect()->route('moderador.inicio', ['tab' => 'empresas'])
            ->with('success', 'Empresa registrada exitosamente.')
            ->with('tab', 'empresas');
    }

    /**
     * Update an existing empresa.
     */
    public function updateEmpresa(Request $request, Empresa $empresa)
    {
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        if ($empresa->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para editar esta empresa.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'responsable' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'correo' => 'required|email|unique:users,email,' . $empresa->user->id,
            'contrasena' => 'nullable|string|min:6|confirmed',
        ]);

        $userData = [
            'email' => $validated['correo'],
        ];
        if (!empty($validated['contrasena'])) {
            $userData['password'] = Hash::make($validated['contrasena']);
        }
        $empresa->user->update($userData);

        $empresa->update([
            'name' => $validated['nombre'],
            'responsable' => $validated['responsable'],
            'telefono' => $validated['telefono'],
        ]);

        return redirect()->route('moderador.inicio', ['tab' => 'empresas'])
            ->with('success', 'Empresa actualizada exitosamente.')
            ->with('tab', 'empresas');
    }

    /**
     * Delete an existing empresa.
     */
    public function deleteEmpresa(Empresa $empresa)
    {
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        if ($empresa->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para eliminar esta empresa.');
        }

        if ($empresa->user) {
            $empresa->user->delete();
        }
        $empresa->delete();

        return redirect()->route('moderador.inicio', ['tab' => 'empresas'])
            ->with('success', 'Empresa eliminada exitosamente.')
            ->with('tab', 'empresas');
    }

    /**
     * Register a new institucion.
     */
    public function registerInstitucion(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();

        Institucion::create([
            'id_user' => null,
            'id_plantel' => $moderador->id_plantel,
            'name' => $validated['nombre'],
        ]);

        return redirect()->route('moderador.inicio', ['tab' => 'instituciones'])
            ->with('success', 'Institución registrada exitosamente.')
            ->with('tab', 'instituciones');
    }

    /**
     * Update an existing institucion.
     */
    public function updateInstitucion(Request $request, Institucion $institucion)
    {
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        if ($institucion->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para editar esta institución.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $institucion->update([
            'name' => $validated['nombre'],
        ]);

        return redirect()->route('moderador.inicio', ['tab' => 'instituciones'])
            ->with('success', 'Institución actualizada exitosamente.')
            ->with('tab', 'instituciones');
    }

    /**
     * Delete an existing institucion.
     */
    public function deleteInstitucion(Institucion $institucion)
    {
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        if ($institucion->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para eliminar esta institución.');
        }

        if ($institucion->user) {
            $institucion->user->delete();
        }
        $institucion->delete();

        return redirect()->route('moderador.inicio', ['tab' => 'instituciones'])
            ->with('success', 'Institución eliminada exitosamente.')
            ->with('tab', 'instituciones');
    }

    /**
     * Register a new maestro and associated user.
     */
    public function registerMaestro(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|unique:users,email',
            'contrasena' => 'required|string|min:6|confirmed',
            'telefono' => 'required|string|max:20',
            'id_institucion' => 'required|exists:instituciones,id',
        ]);

        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        $institucion = Institucion::findOrFail($validated['id_institucion']);
        if ($institucion->id_plantel !== $moderador->id_plantel) {
            abort(403, 'La institución seleccionada no pertenece a tu plantel.');
        }

        $user = User::create([
            'email' => $validated['correo'],
            'password' => Hash::make($validated['contrasena']),
            'type' => 'maestro',
        ]);

        Maestro::create([
            'id_user' => $user->id,
            'id_institucion' => $validated['id_institucion'],
            'id_plantel' => $moderador->id_plantel,
            'name' => $validated['nombre'],
            'telefono' => $validated['telefono'],
        ]);

        return redirect()->route('moderador.inicio', ['tab' => 'maestros'])
            ->with('success', 'Maestro registrado exitosamente.')
            ->with('tab', 'maestros');
    }

    /**
     * Update an existing maestro.
     */
    public function updateMaestro(Request $request, Maestro $maestro)
    {
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        if ($maestro->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para editar este maestro.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'correo' => 'required|email|unique:users,email,' . $maestro->user->id,
            'contrasena' => 'nullable|string|min:6|confirmed',
            'id_institucion' => 'required|exists:instituciones,id',
        ]);

        $institucion = Institucion::findOrFail($validated['id_institucion']);
        if ($institucion->id_plantel !== $moderador->id_plantel) {
            abort(403, 'La institución seleccionada no pertenece a tu plantel.');
        }

        $userData = [
            'email' => $validated['correo'],
        ];
        if (!empty($validated['contrasena'])) {
            $userData['password'] = Hash::make($validated['contrasena']);
        }
        $maestro->user->update($userData);

        $maestro->update([
            'name' => $validated['nombre'],
            'telefono' => $validated['telefono'],
            'id_institucion' => $validated['id_institucion'],
        ]);

        return redirect()->route('moderador.inicio', ['tab' => 'maestros'])
            ->with('success', 'Maestro actualizado exitosamente.')
            ->with('tab', 'maestros');
    }

    /**
     * Delete an existing maestro.
     */
    public function deleteMaestro(Maestro $maestro)
    {
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        if ($maestro->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para eliminar este maestro.');
        }

        if ($maestro->user) {
            $maestro->user->delete();
        }
        $maestro->delete();

        return redirect()->route('moderador.inicio', ['tab' => 'maestros'])
            ->with('success', 'Maestro eliminado exitosamente.')
            ->with('tab', 'maestros');
    }

    /**
     * Register a new especialidad.
     */
    public function registerEspecialidad(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'id_institucion' => 'required|exists:instituciones,id',
        ]);

        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        $institucion = Institucion::findOrFail($validated['id_institucion']);
        if ($institucion->id_plantel !== $moderador->id_plantel) {
            abort(403, 'La institución seleccionada no pertenece a tu plantel.');
        }

        Especialidad::create([
            'name' => $validated['nombre'],
            'id_plantel' => $moderador->id_plantel,
            'id_institucion' => $validated['id_institucion'],
        ]);

        return redirect()->route('moderador.inicio', ['tab' => 'especialidades'])
            ->with('success', 'Especialidad registrada exitosamente.')
            ->with('tab', 'especialidades');
    }

    /**
     * Update an existing especialidad.
     */
    public function updateEspecialidad(Request $request, Especialidad $especialidad)
    {
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        if ($especialidad->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para editar esta especialidad.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'section' => 'required|in:especialidad',
        ]);

        $especialidad->update([
            'name' => $validated['nombre'],
        ]);

        return redirect()->route('moderador.inicio', ['tab' => 'especialidades'])
            ->with('success', 'Especialidad actualizada exitosamente.')
            ->with('tab', 'especialidades');
    }

    /**
     * Delete an existing especialidad.
     */
    public function deleteEspecialidad(Especialidad $especialidad)
    {
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        if ($especialidad->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para eliminar esta especialidad.');
        }

        $especialidad->delete();

        return redirect()->route('moderador.inicio', ['tab' => 'especialidades'])
            ->with('success', 'Especialidad eliminada exitosamente.')
            ->with('tab', 'especialidades');
    }

    /**
     * Register a new alumno and associated user.
     */
    public function registerAlumno(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|unique:users,email',
            'contrasena' => 'required|string|min:6|confirmed',
            'telefono' => 'required|string|max:20',
            'telefono_emergencia' => 'required|string|max:20',
            'lunes' => 'boolean',
            'martes' => 'boolean',
            'miercoles' => 'boolean',
            'jueves' => 'boolean',
            'viernes' => 'boolean',
            'sabado' => 'boolean',
            'domingo' => 'boolean',
            'fecha_inicio' => 'required|date',
            'fecha_termino' => 'required|date|after_or_equal:fecha_inicio',
            'id_empresa' => 'required|exists:empresas,id',
            'id_institucion' => 'required|exists:instituciones,id',
            'id_maestro' => 'required|exists:maestros,id',
            'id_especialidad' => 'required|exists:especialidades,id',
        ]);

        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();

        // Validate that selected empresa, institucion, maestro, and especialidad belong to moderator's plantel
        $empresa = Empresa::findOrFail($validated['id_empresa']);
        $institucion = Institucion::findOrFail($validated['id_institucion']);
        $maestro = Maestro::findOrFail($validated['id_maestro']);
        $especialidad = Especialidad::findOrFail($validated['id_especialidad']);

        if ($empresa->id_plantel !== $moderador->id_plantel ||
            $institucion->id_plantel !== $moderador->id_plantel ||
            $maestro->id_plantel !== $moderador->id_plantel ||
            $especialidad->id_plantel !== $moderador->id_plantel ||
            $maestro->id_institucion !== $institucion->id ||
            $especialidad->id_institucion !== $institucion->id) {
            abort(403, 'Selecciones no válidas para este plantel o institución.');
        }

        $user = User::create([
            'email' => $validated['correo'],
            'password' => Hash::make($validated['contrasena']),
            'type' => 'alumno',
        ]);

        Alumno::create([
            'id_user' => $user->id,
            'id_plantel' => $moderador->id_plantel,
            'id_especialidad' => $validated['id_especialidad'],
            'name' => $validated['nombre'],
            'telefono' => $validated['telefono'],
            'telefono_emergencia' => $validated['telefono_emergencia'],
            'lunes' => $validated['lunes'],
            'martes' => $validated['martes'],
            'miercoles' => $validated['miercoles'],
            'jueves' => $validated['jueves'],
            'viernes' => $validated['viernes'],
            'sabado' => $validated['sabado'],
            'domingo' => $validated['domingo'],
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_termino' => $validated['fecha_termino'],
            'id_empresa' => $validated['id_empresa'],
            'id_maestro' => $validated['id_maestro'],
            'id_institucion' => $validated['id_institucion'],
        ]);

        return redirect()->route('moderador.inicio', ['tab' => 'alumnos'])
            ->with('success', 'Alumno registrado exitosamente.')
            ->with('tab', 'alumnos');
    }

    /**
     * Update an existing alumno.
     */
    public function updateAlumno(Request $request, Alumno $alumno)
    {
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        if ($alumno->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para editar este alumno.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'telefono_emergencia' => 'required|string|max:20',
            'correo' => 'required|email|unique:users,email,' . $alumno->user->id,
            'contrasena' => 'nullable|string|min:6|confirmed',
            'lunes' => 'boolean',
            'martes' => 'boolean',
            'miercoles' => 'boolean',
            'jueves' => 'boolean',
            'viernes' => 'boolean',
            'sabado' => 'boolean',
            'domingo' => 'boolean',
            'fecha_inicio' => 'required|date',
            'fecha_termino' => 'required|date|after_or_equal:fecha_inicio',
            'id_empresa' => 'required|exists:empresas,id',
            'id_institucion' => 'required|exists:instituciones,id',
            'id_maestro' => 'required|exists:maestros,id',
            'id_especialidad' => 'required|exists:especialidades,id',
        ]);

        $empresa = Empresa::findOrFail($validated['id_empresa']);
        $institucion = Institucion::findOrFail($validated['id_institucion']);
        $maestro = Maestro::findOrFail($validated['id_maestro']);
        $especialidad = Especialidad::findOrFail($validated['id_especialidad']);

        if ($empresa->id_plantel !== $moderador->id_plantel ||
            $institucion->id_plantel !== $moderador->id_plantel ||
            $maestro->id_plantel !== $moderador->id_plantel ||
            $especialidad->id_plantel !== $moderador->id_plantel ||
            $maestro->id_institucion !== $institucion->id ||
            $especialidad->id_institucion !== $institucion->id) {
            abort(403, 'Selecciones no válidas para este plantel o institución.');
        }

        $userData = [
            'email' => $validated['correo'],
        ];
        if (!empty($validated['contrasena'])) {
            $userData['password'] = Hash::make($validated['contrasena']);
        }
        $alumno->user->update($userData);

        $alumno->update([
            'name' => $validated['nombre'],
            'telefono' => $validated['telefono'],
            'telefono_emergencia' => $validated['telefono_emergencia'],
            'lunes' => $validated['lunes'],
            'martes' => $validated['martes'],
            'miercoles' => $validated['miercoles'],
            'jueves' => $validated['jueves'],
            'viernes' => $validated['viernes'],
            'sabado' => $validated['sabado'],
            'domingo' => $validated['domingo'],
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_termino' => $validated['fecha_termino'],
            'id_empresa' => $validated['id_empresa'],
            'id_maestro' => $validated['id_maestro'],
            'id_institucion' => $validated['id_institucion'],
            'id_especialidad' => $validated['id_especialidad'],
        ]);

        return redirect()->route('moderador.inicio', ['tab' => 'alumnos'])
            ->with('success', 'Alumno actualizado exitosamente.')
            ->with('tab', 'alumnos');
    }

    /**
     * Delete an existing alumno.
     */
    public function deleteAlumno(Alumno $alumno)
    {
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        if ($alumno->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para eliminar este alumno.');
        }

        $alumno->user->delete();
        $alumno->delete();

        return redirect()->route('moderador.inicio', ['tab' => 'alumnos'])
            ->with('success', 'Alumno eliminado exitosamente.')
            ->with('tab', 'alumnos');
    }

    /**
     * Fetch maestros by institucion ID.
     */
    public function getMaestrosPorInstitucion($institucionId)
    {
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        $institucion = Institucion::findOrFail($institucionId);

        if ($institucion->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para acceder a esta institución.');
        }

        $maestros = Maestro::where('id_institucion', $institucionId)->get(['id', 'name']);
        return response()->json($maestros);
    }

    /**
     * Fetch especialidades by institucion ID.
     */
    public function getEspecialidadesPorInstitucion($institucionId)
    {
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        $institucion = Institucion::findOrFail($institucionId);

        if ($institucion->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para acceder a esta institución.');
        }

        $especialidades = Especialidad::where('id_institucion', $institucionId)->get(['id', 'name']);
        return response()->json($especialidades);
    }
    public function guardarListaAlumno(Request $request, Alumno $alumno)
    {
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        if ($alumno->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para editar este alumno.');
        }

        $validated = $request->validate([
            'fecha' => 'required|date',
            'estado' => 'required|in:asistencia,falta,justificado',
        ]);

        $fecha = Carbon::parse($validated['fecha']);
        if ($fecha->isFuture()) {
            return back()->with('error', 'No se puede modificar lista a futuro.')
                        ->with('tab', 'alumnos');
        }

        if ($alumno->fecha_inicio && $fecha->lt($alumno->fecha_inicio)) {
            return back()->with('error', 'Fecha fuera del rango permitido.')
                         ->with('tab', 'alumnos');
        }

        if ($alumno->fecha_termino && $fecha->gt($alumno->fecha_termino)) {
            return back()->with('error', 'Fecha fuera del rango permitido.')
                         ->with('tab', 'alumnos');
        }

        $diaSemana = $fecha->dayOfWeek; // 0 (domingo) - 6 (sabado)
        $diasPermitidos = [
            0 => $alumno->domingo,
            1 => $alumno->lunes,
            2 => $alumno->martes,
            3 => $alumno->miercoles,
            4 => $alumno->jueves,
            5 => $alumno->viernes,
            6 => $alumno->sabado,
        ];
        if (empty($diasPermitidos[$diaSemana])) {
            return back()->with('error', 'El alumno no debe asistir este día.')
                         ->with('tab', 'alumnos');
        }
        
        Lista::updateOrCreate(
            [
                'id_alumno'  => $alumno->id,
                'id_empresa' => $alumno->id_empresa,
                'fecha'      => $fecha->toDateString(),
            ],
            [ 'estado' => $validated['estado'] ]
        );

        return back()->with('success', 'Lista actualizada.')
                     ->with('tab', 'alumnos');
    }

    public function generarReporteAlumnoPdf(Request $request, Alumno $alumno)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $alumno->load(['especialidad', 'institucion', 'maestro', 'empresa']);

        $fi = Carbon::parse($request->input('fecha_inicio'))->startOfDay();
        $ff = Carbon::parse($request->input('fecha_fin'))->endOfDay();

        $listas = Lista::where('id_alumno', $alumno->id)
            ->whereBetween('fecha', [$fi, $ff])
            ->get();

        $entregasPeriodo = Entrega::where('id_alumno', $alumno->id)
            ->whereBetween('created_at', [$fi, $ff])
            ->get();

        $totalExpected = 0;
        $attendanceDetails = [];
        for ($d = $fi->copy(); $d <= $ff; $d->addDay()) {
            $dayMap = [0 => 'domingo', 1 => 'lunes', 2 => 'martes', 3 => 'miercoles', 4 => 'jueves', 5 => 'viernes', 6 => 'sabado'];
            $day = $dayMap[$d->dayOfWeek];
            if ($d->between($alumno->fecha_inicio, $alumno->fecha_termino) && $alumno->$day) {
                $totalExpected++;
                $registro = $listas->first(function ($lista) use ($d) {
                    return $lista->fecha->toDateString() === $d->toDateString();
                });
                $estado = $registro->estado ?? 'sin registro';
                $attendanceDetails[] = ['fecha' => $d->toDateString(), 'estado' => $estado];
            }
        }

        $attendanceSummary = [
            'asistencia'   => $listas->where('estado', 'asistencia')->count(),
            'falta'        => $listas->where('estado', 'falta')->count(),
            'justificado'  => $listas->where('estado', 'justificado')->count(),
        ];
        $attendanceSummary['no_registro'] = max(0, $totalExpected - $listas->count());
        $attendanceSummary['total'] = $totalExpected;

        $totalSubtemas = $alumno->plan ? $alumno->plan->temas->sum(fn($t) => $t->subtemas->count()) : 0;
        $entregasTotales = Entrega::where('id_alumno', $alumno->id)->get()->unique('id_subtema');
        $entregasPeriodoU = $entregasPeriodo->unique('id_subtema');

        $taskSummary = [
            'en_periodo'    => $entregasPeriodoU->count(),
            'fuera_periodo' => max(0, $entregasTotales->count() - $entregasPeriodoU->count()),
            'pendientes'    => max(0, $totalSubtemas - $entregasTotales->count()),
            'total'         => $totalSubtemas,
        ];

        $logoPath = public_path('images/lgo.png');
        $logo = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));

        $attendanceChartConfig = [
            'type' => 'pie',
            'data' => [
                'labels' => ['Asistencia', 'Justificado', 'Falta', 'Sin registro'],
                'datasets' => [[
                    'data' => [
                        $attendanceSummary['asistencia'],
                        $attendanceSummary['justificado'],
                        $attendanceSummary['falta'],
                        $attendanceSummary['no_registro'],
                    ],
                    'backgroundColor' => ['#16a34a', '#2563eb', '#dc2626', '#9ca3af'],
                ]],
            ],
            'options' => [ 'plugins' => [ 'legend' => [ 'position' => 'bottom' ] ] ],
        ];

        $tasksChartConfig = [
            'type' => 'pie',
            'data' => [
                'labels' => ['Entregadas en periodo', 'Entregadas fuera', 'Pendientes'],
                'datasets' => [[
                    'data' => [
                        $taskSummary['en_periodo'],
                        $taskSummary['fuera_periodo'],
                        $taskSummary['pendientes'],
                    ],
                    'backgroundColor' => ['#16a34a', '#2563eb', '#9ca3af'],
                ]],
            ],
            'options' => [ 'plugins' => [ 'legend' => [ 'position' => 'bottom' ] ] ],
        ];

        $attendanceChartUrl = 'https://quickchart.io/chart?c=' . urlencode(json_encode($attendanceChartConfig));
        $tasksChartUrl = 'https://quickchart.io/chart?c=' . urlencode(json_encode($tasksChartConfig));

        $html = view('moderador.reportes.pdf', [
            'alumno'           => $alumno,
            'fi'               => $fi,
            'ff'               => $ff,
            'attendanceSummary' => $attendanceSummary,
            'attendanceDetails' => $attendanceDetails,
            'entregasPeriodo'   => $entregasPeriodo,
            'logo'              => $logo,
            'attendanceChartUrl' => $attendanceChartUrl,
            'tasksChartUrl'      => $tasksChartUrl,
        ])->render();

        $pdf = new Mpdf();
        $pdf->WriteHTML($html);

        $directory = storage_path('app/reportes');
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $fileName = 'reporte_' . $alumno->id . '_' . now()->format('Ymd_His') . '.pdf';
        $path = $directory . '/' . $fileName;
        $pdf->Output($path, \Mpdf\Output\Destination::FILE);

        return response()->download($path);
    }
}