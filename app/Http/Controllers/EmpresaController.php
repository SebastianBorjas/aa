<?php

namespace App\Http\Controllers;
use App\Models\Empresa;
use App\Models\Lista;
use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Entrega;

class EmpresaController extends Controller
{
    public function inicio(Request $request)
    {
        return view('empresa.inicio', [
            'fecha' => $request->query('fecha') ?? now()->toDateString(),
            'revisionPendientes' => $this->conteoPendientesRevision(),
        ]);
    }

    public function guardarLista(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'estado' => 'array',
        ]);

        $fecha = Carbon::parse($request->fecha);
        if ($fecha->isFuture()) {
            return back()->with('error', 'No se puede pasar lista a futuro.');
        }

        $empresa = Empresa::where('id_user', Auth::id())->firstOrFail();

        foreach ($request->estado as $alumnoId => $estado) {
            Lista::updateOrCreate(
                [
                    'id_alumno'  => $alumnoId,
                    'id_empresa' => $empresa->id,
                    'fecha'      => $fecha->toDateString(),
                ],
                ['estado' => $estado]
            );
        }

        return redirect()
            ->route('empresa.inicio', ['tab' => 'lista', 'fecha' => $fecha->toDateString()])
            ->with('success', 'Lista guardada');
    }
    public function verificarEntrega(Request $request, Entrega $entrega)
    {
        $empresa = Empresa::where('id_user', Auth::id())->firstOrFail();

        if ($entrega->alumno->id_empresa !== $empresa->id) {
            abort(403);
        }

        $request->validate([
            'comentario' => 'nullable|string',
        ]);

        $entrega->update([
            'estado' => 'pen_mae',
            'vce' => $request->comentario,
        ]);

        return back()->with('success', 'Entrega enviada al maestro.');
    }

    public function rechazarEntrega(Request $request, Entrega $entrega)
    {
        $empresa = Empresa::where('id_user', Auth::id())->firstOrFail();

        if ($entrega->alumno->id_empresa !== $empresa->id) {
            abort(403);
        }

        $request->validate([
            'comentario' => 'nullable|string',
        ]);

        $entrega->update([
            'estado' => 'rechazado',
            'rce' => $request->comentario,
        ]);

        return back()->with('success', 'Entrega rechazada.');
    }
    public function guardarListaAlumno(Request $request, Alumno $alumno)
    {
        $empresa = Empresa::where('id_user', Auth::id())->firstOrFail();
        if ($alumno->id_empresa !== $empresa->id) {
            abort(403);
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

        $diaSemana = $fecha->dayOfWeek;
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
                'id_empresa' => $empresa->id,
                'fecha'      => $fecha->toDateString(),
            ],
            ['estado' => $validated['estado']]
        );

        return back()->with('success', 'Lista actualizada.')
                     ->with('tab', 'alumnos');
    }

    private function conteoPendientesRevision(): int
    {
        $empresa = Empresa::where('id_user', Auth::id())->first();
        if (!$empresa) {
            return 0;
        }

        return Entrega::where('estado', 'pen_emp')
            ->whereHas('alumno', function ($q) use ($empresa) {
                $q->where('id_empresa', $empresa->id);
            })
            ->count();
    }
}
