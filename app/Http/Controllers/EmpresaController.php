<?php

namespace App\Http\Controllers;
use App\Models\Empresa;
use App\Models\Lista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmpresaController extends Controller
{
    public function inicio(Request $request)
    {
        return view('empresa.inicio', [
            'fecha' => $request->query('fecha') ?? now()->toDateString(),
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
}
