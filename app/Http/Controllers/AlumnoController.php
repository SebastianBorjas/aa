<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    public function inicio(Request $request)
    {
        $tab = $request->query('tab', 'tareas');
        return view('alumno.inicio', compact('tab'));
    }
}