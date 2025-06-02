<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Tema;
use App\Models\Subtema;
use App\Models\Maestro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaestroController extends Controller
{
    public function inicio(Request $request)
    {
        $tab = $request->query('tab', 'alumnos');
        return view('maestro.inicio', compact('tab'));
    }
}
