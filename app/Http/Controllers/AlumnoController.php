<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Alumno;
class AlumnoController extends Controller
{
    public function inicio(Request $request)
    {
        $tab = $request->query('tab', 'tareas');
        $alumno = Alumno::where('id_user', Auth::id())
            ->with(['plan.temas.subtemas'])
            ->first();

        return view('alumno.inicio', compact('tab', 'alumno'));
    }
}