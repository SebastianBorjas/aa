<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MaestroController extends Controller
{
    public function inicio(Request $request)
    {
        $tab = $request->query('tab', 'alumnos');
        return view('maestro.inicio', compact('tab'));
    }
}