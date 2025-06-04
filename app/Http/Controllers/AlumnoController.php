<?php

namespace App\Http\Controllers;
use App\Models\Entrega;
use App\Models\Subtema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Alumno;
class AlumnoController extends Controller
{
    public function inicio(Request $request)
    {
        $tab = $request->query('tab', 'tareas');
        $alumno = Alumno::where('id_user', Auth::id())->first();

        if ($alumno) {
            $alumno->load(['plan.temas.subtemas.entregas' => function ($q) use ($alumno) {
                $q->where('id_alumno', $alumno->id);
            }]);
        }

        return view('alumno.inicio', compact('tab', 'alumno'));
    }
    public function entregarTarea(Request $request, $subtemaId)
    {
        $alumno = Alumno::where('id_user', Auth::id())->firstOrFail();
        Subtema::findOrFail($subtemaId);

        if (Entrega::where('id_subtema', $subtemaId)->where('id_alumno', $alumno->id)->exists()) {
            return back()->with('error', 'Ya enviaste esta tarea.');
        }

        $request->validate([
            'contenido' => 'required|string',
            'archivos.*' => 'file|max:2048',
        ]);

        if ($request->hasFile('archivos') && count($request->file('archivos')) > 4) {
            return back()->with('error', 'Máx. 4 archivos.');
        }

        $rutas = [];
        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $file) {
                $rutas[] = $file->store('entregas', 'public');
            }
        }

        Entrega::create([
            'id_subtema' => $subtemaId,
            'id_alumno' => $alumno->id,
            'contenido' => $request->contenido,
            'rutas' => $rutas,
        ]);

        return redirect()->route('alumno.inicio', ['tab' => 'tareas'])->with('success', 'Tarea enviada');
    }
}