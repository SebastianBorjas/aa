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
    // Panel principal
    public function inicio(Request $request)
    {
        $tab = $request->query('tab', 'planes');
        $subtab = $request->query('subtab', 'crear_plan');
        return view('maestro.inicio', compact('tab', 'subtab'));
    }

    // Mostrar vista crear plan (retorna lista de planes)
    public function planesCrear()
    {
        $userId = Auth::id();
        $maestro = Maestro::where('id_user', $userId)->first();
        $planes = $maestro
            ? Plan::where('id_maestro', $maestro->id)
                ->with(['temas.subtemas'])
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        return view('maestro.partials.planes.crear_plan', compact('planes'));
    }

    // Crear plan
    public function crearPlan(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);
        $maestro = Maestro::where('id_user', Auth::id())->first();
        if (!$maestro) return back()->with('error', 'No tienes un perfil de maestro.');

        Plan::create([
            'id_maestro' => $maestro->id,
            'nombre' => $request->nombre,
        ]);
        return redirect()->route('maestro.inicio', ['tab' => 'planes', 'subtab' => 'crear_plan'])
                         ->with('success', 'Plan creado exitosamente');
    }

    // Editar nombre de plan
    public function actualizarPlan(Request $request, $id)
    {
        $request->validate(['nombre' => 'required|string|max:255']);
        $plan = Plan::findOrFail($id);
        $plan->update(['nombre' => $request->nombre]);
        return redirect()->route('maestro.inicio', [
            'tab' => 'planes',
            'subtab' => 'crear_plan',
            'planEdit' => $request->id_plan ?? Tema::find($request->id_tema)->id_plan ?? null
        ])->with('success', 'Tema guardado');

    }

    // Eliminar plan (y en cascada temas/subtemas)
    public function eliminarPlan($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete(); // Si tu bd está bien, eliminará hijos por FK ON DELETE CASCADE
        return back()->with('success', 'Plan eliminado');
    }

    // Crear o actualizar tema
    public function guardarTema(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'id_plan' => 'required|integer|exists:planes,id',
            'descripcion' => 'nullable|string',
            'id' => 'nullable|integer|exists:temas,id',
        ]);
        if ($request->filled('id')) {
            $tema = Tema::findOrFail($request->id);
            $tema->update($request->only(['nombre', 'descripcion']));
        } else {
            Tema::create($request->only(['id_plan', 'nombre', 'descripcion']));
        }
        return redirect()->route('maestro.inicio', [
            'tab' => 'planes',
            'subtab' => 'crear_plan',
            'planEdit' => $request->id_plan ?? Tema::find($request->id_tema)->id_plan ?? null
        ])->with('success', 'Tema guardado');

    }

    // Eliminar tema
    public function eliminarTema($id)
    {
        $tema = Tema::findOrFail($id);
        $tema->delete();
        return redirect()->route('maestro.inicio', [
            'tab' => 'planes',
            'subtab' => 'crear_plan',
            'planEdit' => $tema->id_plan
        ])->with('success', 'Tema eliminado');
    }

    // Crear o actualizar subtema
    public function guardarSubtema(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'id_tema' => 'required|integer|exists:temas,id',
            'descripcion' => 'nullable|string',
            'id' => 'nullable|integer|exists:subtemas,id',
        ]);
        if ($request->filled('id')) {
            $subtema = Subtema::findOrFail($request->id);
            $subtema->update($request->only(['nombre', 'descripcion']));
        } else {
            Subtema::create($request->only(['id_tema', 'nombre', 'descripcion']));
        }
        return redirect()->route('maestro.inicio', [
            'tab' => 'planes',
            'subtab' => 'crear_plan',
            'planEdit' => $request->id_plan ?? Tema::find($request->id_tema)->id_plan ?? null
        ])->with('success', 'Tema guardado');
    }

    // Eliminar subtema
    public function eliminarSubtema($id)
    {
        $subtema = Subtema::findOrFail($id);
        $subtema->delete();
        return redirect()->route('maestro.inicio', [
            'tab' => 'planes',
            'subtab' => 'crear_plan',
            'planEdit' => $subtema->id_tema ? Tema::find($subtema->id_tema)->id_plan : null
        ])->with('success', 'Subtema eliminado');
    }
}
