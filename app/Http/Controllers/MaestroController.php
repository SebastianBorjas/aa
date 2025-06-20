<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Tema;
use App\Models\Subtema;
use App\Models\Maestro;
use App\Models\Alumno;
use App\Models\Entrega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;



class MaestroController extends Controller
{
    // Panel principal
    public function inicio(Request $request)
    {
        $tab = $request->query('tab', 'planes');
        $subtab = $request->query('subtab', 'crear_plan');
        return view('maestro.inicio', [
            'tab' => $tab,
            'subtab' => $subtab,
            'revisionPendientes' => $this->conteoPendientesRevision(),
        ]);
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

        return view('maestro.partials.planes.crear_plan', [
            'planes' => $planes,
            'revisionPendientes' => $this->conteoPendientesRevision(),
        ]);
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

    // Guardar o actualizar subtema (sin archivos)
    public function guardarSubtema(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'id_tema'     => 'required|integer|exists:temas,id',
            'descripcion' => 'nullable|string',
            'id'          => 'nullable|integer|exists:subtemas,id',
            'archivos.*'  => 'file|max:2048',
        ]);

        if ($request->filled('id')) {
            $subtema = Subtema::findOrFail($request->id);
            $subtema->update($request->only(['nombre', 'descripcion']));
        } else {
            $subtema = Subtema::create($request->only(['id_tema', 'nombre', 'descripcion']));
        }

        $files = $request->file('archivos', []);
        if (!is_array($files)) {
            $files = $files ? [$files] : [];
        }

        if (!empty($files)) {
            $rutas = $subtema->rutas ?: [];
            if (count($rutas) >= 4 || count($rutas) + count($files) > 4) {
                return back()->with('error', 'Solo se permiten máximo 4 archivos por subtema');
            }
            foreach ($files as $file) {
                $rutas[] = $file->store('subtemas', 'public');
            }
            $subtema->rutas = $rutas;
            $subtema->save();
        }

        return redirect()->back()->with('success', 'Subtema guardado');
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
    // AGREGAR ARCHIVOS a subtema
    public function subtemaAgregarArchivo(Request $request, $id)
    {
        $request->validate([
            'archivos.*' => 'file|max:2048', // 2MB
        ]);

        $subtema = Subtema::findOrFail($id);

        $rutas = $subtema->rutas ?: [];
        $files = $request->file('archivos', []);
        if (!is_array($files)) {
            $files = $files ? [$files] : [];
        }

        if (count($rutas) >= 4 || count($rutas) + count($files) > 4) {
            return back()->with('error', 'Solo se permiten máximo 4 archivos por subtema');
        }

        
        foreach ($files as $file) {
            $rutas[] = $file->store('subtemas', 'public');
        }

        $subtema->rutas = $rutas;
        $subtema->save();

        return back()->with('success', 'Archivo subido correctamente');
    }
    // ELIMINAR ARCHIVO de subtema
    public function subtemaEliminarArchivo(Request $request, $id)
    {
        $request->validate([
            'file_index' => 'required|integer|min:0',
        ]);
        $subtema = Subtema::findOrFail($id);
        $rutas = $subtema->rutas ?: [];
        $index = $request->file_index;

        if (isset($rutas[$index])) {
            // Elimina archivo físico
            Storage::disk('public')->delete($rutas[$index]);
            // Elimina ruta del array
            array_splice($rutas, $index, 1);
            $subtema->rutas = $rutas;
            $subtema->save();
        }
        return back()->with('success', 'Archivo eliminado');
    }
    public function asignarPlan(Request $request)
    {
        $request->validate([
            'plan' => 'required|integer|exists:planes,id',
            'alumnos' => 'required|array',
            'alumnos.*' => 'integer|exists:alumnos,id',
        ]);

        $maestro = Maestro::where('id_user', Auth::id())->first();
        if (!$maestro) {
            return back()->with('error', 'No tienes un perfil de maestro.');
        }

        // Verifica que el plan pertenezca al maestro
        $plan = Plan::where('id', $request->plan)
            ->where('id_maestro', $maestro->id)
            ->firstOrFail();

        // Actualiza solo alumnos del maestro
        Alumno::whereIn('id', $request->alumnos)
            ->where('id_maestro', $maestro->id)
            ->update(['id_plan' => $plan->id]);

        return redirect()->route('maestro.inicio', [
            'tab' => 'planes',
            'subtab' => 'asignar_plan',
        ])->with('success', 'Plan asignado correctamente');
    }

    public function verificarEntrega(Request $request, Entrega $entrega)
    {
        $maestro = Maestro::where('id_user', Auth::id())->firstOrFail();

        if ($entrega->alumno->id_maestro !== $maestro->id) {
            abort(403);
        }

        $request->validate([
            'comentario' => 'nullable|string',
        ]);

        $entrega->update([
            'estado' => 'verificado',
            'vcm' => $request->comentario,
        ]);

        return back()->with('success', 'Entrega verificada.');
    }

    public function rechazarEntrega(Request $request, Entrega $entrega)
    {
        $maestro = Maestro::where('id_user', Auth::id())->firstOrFail();

        if ($entrega->alumno->id_maestro !== $maestro->id) {
            abort(403);
        }

        $request->validate([
            'comentario' => 'nullable|string',
        ]);

        $entrega->update([
            'estado' => 'rechazado',
            'rcm' => $request->comentario,
        ]);

        return back()->with('success', 'Entrega rechazada.');
    }
    // Vistas detalladas
    public function verPlan(Plan $plan)
    {
        $plan->load('temas');
        return view('maestro.partials.planes.plan_show', [
            'plan' => $plan,
            'revisionPendientes' => $this->conteoPendientesRevision(),
        ]);
    }

    public function verTema(Tema $tema)
    {
        $tema->load('subtemas');
        return view('maestro.partials.planes.tema_show', [
            'tema' => $tema,
            'revisionPendientes' => $this->conteoPendientesRevision(),
        ]);
    }

    public function verSubtema(Subtema $subtema)
    {
        return view('maestro.partials.planes.subtema_show', [
            'subtema' => $subtema,
            'revisionPendientes' => $this->conteoPendientesRevision(),
        ]);
    }

    public function formCrearSubtema(Tema $tema)
    {
        return view('maestro.partials.planes.subtema_create', [
            'tema' => $tema,
            'revisionPendientes' => $this->conteoPendientesRevision(),
        ]);
    }

    private function conteoPendientesRevision(): int
    {
        $maestro = Maestro::where('id_user', Auth::id())->first();
        if (!$maestro) {
            return 0;
        }

        return Entrega::where('estado', 'pen_mae')
            ->whereHas('alumno', function ($q) use ($maestro) {
                $q->where('id_maestro', $maestro->id);
            })
            ->count();
    }
}
