<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plantel;
use App\Models\Moderador;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function inicio()
    {
        $planteles = Plantel::with('moderadores.user')->get();
        return view('admin.inicio', [
            'planteles' => $planteles,
            'plantelesSelect' => Plantel::all(),
        ]);
    }

    public function registrarPlantel(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|unique:planteles,nombre',
        ]);

        $plantel = Plantel::create($validated);

        return response()->json(['plantel' => $plantel]);
    }

    public function registrarModerador(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6', // Solo requerido en registro
            'id_plantel' => 'required|exists:planteles,id',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'type' => 'moderador',
                'email_verified_at' => now(),
            ]);

            Moderador::create([
                'id_user' => $user->id,
                'id_plantel' => $validated['id_plantel'],
                'name' => $validated['name'],
            ]);
        });

        return response()->json(['message' => 'Moderador registrado']);
    }

    public function eliminarPlantel($id)
    {
        $plantel = Plantel::findOrFail($id);
        $plantel->delete();

        return response()->json(['message' => 'Plantel eliminado']);
    }

    public function actualizarPlantel(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|unique:planteles,nombre,' . $id,
        ]);

        $plantel = Plantel::findOrFail($id);
        $plantel->update($validated);

        return response()->json(['message' => 'Plantel actualizado']);
    }

    public function eliminarModerador($id)
    {
        $moderador = Moderador::findOrFail($id);
        $userId = $moderador->user->id;
        DB::transaction(function () use ($moderador, $userId) {
            $moderador->user->delete();
            $moderador->delete();
        });

        return response()->json(['message' => 'Moderador eliminado']);
    }

    public function actualizarModerador(Request $request, $id)
    {
        try {
            $userId = $request->header('X-User-ID');
            if (!$userId) {
                return response()->json(['message' => 'Falta el ID del usuario en el header X-User-ID'], 400);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $userId,
                'password' => 'sometimes|string', // Sin min:6, solo valida si se envía
            ]);

            $moderador = Moderador::findOrFail($id);
            $user = User::findOrFail($userId);

            DB::transaction(function () use ($moderador, $user, $validated) {
                $moderador->update(['name' => $validated['name']]);
                $user->update([
                    'email' => $validated['email'],
                    'password' => isset($validated['password']) ? Hash::make($validated['password']) : $user->password,
                ]);
            });

            return response()->json(['message' => 'Moderador actualizado']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar moderador: ' . $e->getMessage()], 500);
        }
    }
}