<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Alumno;
use App\Models\Subtema;
use App\Models\Entrega;
use Illuminate\Support\Facades\Storage;

class AlumnoController extends Controller
{
    public function inicio(Request $request)
    {
        $tab = $request->query('tab', 'tareas');
        $alumno = Alumno::where('id_user', Auth::id())->first();

        if ($alumno) {
            $alumno->load([
                'plan.temas.subtemas' => function ($query) use ($alumno) {
                    $query->with(['entregas' => function ($q) use ($alumno) {
                        $q->where('id_alumno', $alumno->id);
                    }]);
                },
            ]);
        }

        return view('alumno.inicio', compact('tab', 'alumno'));
    }

    public function entregarTarea(Request $request, $subtema)
    {
        $alumno = Alumno::where('id_user', Auth::id())->firstOrFail();
        $subtema = Subtema::findOrFail($subtema);

        $request->validate([
            'contenido' => 'required|string',
            'archivos' => 'nullable|array|max:4',
            'archivos.*' => 'file|max:2048',
        ]);

        if (Entrega::where('id_subtema', $subtema->id)->where('id_alumno', $alumno->id)->exists()) {
            return back()->with('error', 'Ya enviaste esta tarea');
        }

        $paths = [];
        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $file) {
                $paths[] = $file->store('entregas', 'public');
            }
        }

        Entrega::create([
            'id_subtema' => $subtema->id,
            'id_alumno' => $alumno->id,
            'contenido' => $request->contenido,
            'rutas' => $paths,
        ]);

        return back()->with('success', 'Tarea enviada');
    }
}