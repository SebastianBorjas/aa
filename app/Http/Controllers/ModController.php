<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\User;
use App\Models\Moderador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ModController extends Controller
{
    /**
     * Register a new empresa and associated user.
     */
    public function registerEmpresa(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|unique:users,email',
            'contrasena' => 'required|string|min:6|confirmed',
            'telefono' => 'required|string|max:20',
        ]);

        // Get the current moderator's id_plantel
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();

        // Create the user
        $user = User::create([
            'email' => $validated['correo'],
            'password' => Hash::make($validated['contrasena']), // Hashing handled by model cast
            'type' => 'moderador',
        ]);

        // Create the empresa
        Empresa::create([
            'id_user' => $user->id,
            'id_plantel' => $moderador->id_plantel,
            'name' => $validated['nombre'],
            'telefono' => $validated['telefono'],
        ]);

        return redirect()->route('moderador.inicio', ['tab' => 'empresas'])
            ->with('success', 'Empresa registrada exitosamente.');
    }

    /**
     * Update an existing empresa.
     */
    public function updateEmpresa(Request $request, Empresa $empresa)
    {
        // Ensure the moderator can only edit empresas from their plantel
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        if ($empresa->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para editar esta empresa.');
        }

        // Validate the request
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'correo' => 'required|email|unique:users,email,' . $empresa->user->id,
            'contrasena' => 'nullable|string|min:6|confirmed',
        ]);

        // Update the user
        $userData = [
            'email' => $validated['correo'],
        ];
        if (!empty($validated['contrasena'])) {
            $userData['password'] = Hash::make($validated['contrasena']);
        }
        $empresa->user->update($userData);

        // Update the empresa
        $empresa->update([
            'name' => $validated['nombre'],
            'telefono' => $validated['telefono'],
        ]);

        return redirect()->route('moderador.inicio', ['tab' => 'empresas'])
            ->with('success', 'Empresa actualizada exitosamente.');
    }

    /**
     * Delete an existing empresa and its associated user.
     */
    public function deleteEmpresa(Empresa $empresa)
    {
        // Ensure the moderator can only delete empresas from their plantel
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        if ($empresa->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para eliminar esta empresa.');
        }

        // Delete the associated user
        $empresa->user->delete();

        // Delete the empresa
        $empresa->delete();

        return redirect()->route('moderador.inicio', ['tab' => 'empresas'])
            ->with('success', 'Empresa eliminada exitosamente.');
    }
}