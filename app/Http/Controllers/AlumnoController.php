<?php

namespace App\Http\Controllers;
use App\Models\Entrega;
use App\Models\Subtema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Alumno;
use Illuminate\Support\Facades\Storage;
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

        $entrega = Entrega::where('id_subtema', $subtemaId)
            ->where('id_alumno', $alumno->id)
            ->first();

        if ($entrega && $entrega->estado !== 'rechazado') {
            return back()->with('error', 'Ya enviaste esta tarea.');
        }

        $request->validate([
            'contenido' => 'required|string',
            'archivos.*' => 'file|max:2048',
        ]);

        if ($request->hasFile('archivos') && count($request->file('archivos')) > 4) {
            return back()->with('error', 'Máx. 4 archivos.');
        }

        $rutasActuales = $entrega?->rutas ?? [];
        // Eliminar archivos seleccionados
        $eliminar = $request->input('delete_files', []);
        foreach ($eliminar as $index) {
            if (isset($rutasActuales[$index])) {
                Storage::disk('public')->delete($rutasActuales[$index]);
                unset($rutasActuales[$index]);
            }
        }
        $rutasActuales = array_values($rutasActuales);
        $rutasNuevas = [];
        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $file) {
                $rutasNuevas[] = $file->store('entregas', 'public');
            }
        }

        if (count($rutasActuales) + count($rutasNuevas) > 4) {
            return back()->with('error', 'Máx. 4 archivos.');
        }

        $rutas = array_merge($rutasActuales, $rutasNuevas);

        if ($entrega) {
            $entrega->update([
                'contenido' => $request->contenido,
                'rutas' => $rutas,
                'estado' => 'pen_emp',
                'rce' => null,
                'rcm' => null,
            ]);
        } else {
            Entrega::create([
                'id_subtema' => $subtemaId,
                'id_alumno' => $alumno->id,
                'contenido' => $request->contenido,
                'rutas' => $rutas,
                'estado' => 'pen_emp',
            ]);
        }

        return redirect()->route('alumno.inicio', ['tab' => 'tareas'])->with('success', 'Tarea enviada');
    }

}