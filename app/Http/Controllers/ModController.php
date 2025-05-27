<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Institucion;
use App\Models\Maestro;
use App\Models\Especialidad;
use App\Models\User;
use App\Models\Moderador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ModController extends Controller
{
    /**
     * Display the moderator dashboard with the specified tab.
     */
    public function inicio(Request $request)
    {
        return view('moderador.inicio', [
            'tab' => $request->query('tab', 'empresas'),
        ]);
    }

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
            'password' => Hash::make($validated['contrasena']),
            'type' => 'empresa',
        ]);

        // Create the empresa
        Empresa::create([
            'id_user' => $user->id,
            'id_plantel' => $moderador->id_plantel,
            'name' => $validated['nombre'],
            'telefono' => $validated['telefono'],
        ]);

        return redirect()->route('moderador.inicio', ['tab' => 'empresas'])
            ->with('success', 'Empresa registrada exitosamente.')
            ->with('tab', 'empresas');
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
            ->with('success', 'Empresa actualizada exitosamente.')
            ->with('tab', 'empresas');
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
            ->with('success', 'Empresa eliminada exitosamente.')
            ->with('tab', 'empresas');
    }

    /**
     * Register a new institucion.
     */
    public function registerInstitucion(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        // Get the current moderator's id_plantel
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();

        // Create the institucion
        Institucion::create([
            'id_user' => null,
            'id_plantel' => $moderador->id_plantel,
            'name' => $validated['nombre'],
        ]);

        return redirect()->route('moderador.inicio', ['tab' => 'instituciones'])
            ->with('success', 'Institución registrada exitosamente.')
            ->with('tab', 'instituciones');
    }

    /**
     * Update an existing institucion.
     */
    public function updateInstitucion(Request $request, Institucion $institucion)
    {
        // Ensure the moderator can only edit instituciones from their plantel
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        if ($institucion->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para editar esta institución.');
        }

        // Validate the request
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        // Update the institucion
        $institucion->update([
            'name' => $validated['nombre'],
        ]);

        return redirect()->route('moderador.inicio', ['tab' => 'instituciones'])
            ->with('success', 'Institución actualizada exitosamente.')
            ->with('tab', 'instituciones');
    }

    /**
     * Delete an existing institucion.
     */
    public function deleteInstitucion(Institucion $institucion)
    {
        // Ensure the moderator can only delete instituciones from their plantel
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        if ($institucion->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para eliminar esta institución.');
        }

        // Delete the institucion
        $institucion->delete();

        return redirect()->route('moderador.inicio', ['tab' => 'instituciones'])
            ->with('success', 'Institución eliminada exitosamente.')
            ->with('tab', 'instituciones');
    }

    /**
     * Register a new maestro and associated user.
     */
    public function registerMaestro(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|unique:users,email',
            'contrasena' => 'required|string|min:6|confirmed',
            'telefono' => 'required|string|max:20',
            'id_institucion' => 'required|exists:instituciones,id',
        ]);

        // Get the current moderator's id_plantel
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();

        // Ensure the selected institucion belongs to the same plantel
        $institucion = Institucion::findOrFail($validated['id_institucion']);
        if ($institucion->id_plantel !== $moderador->id_plantel) {
            abort(403, 'La institución seleccionada no pertenece a tu plantel.');
        }

        // Create the user
        $user = User::create([
            'email' => $validated['correo'],
            'password' => Hash::make($validated['contrasena']),
            'type' => 'maestro',
        ]);

        // Create the maestro
        Maestro::create([
            'id_user' => $user->id,
            'id_institucion' => $validated['id_institucion'],
            'id_plantel' => $moderador->id_plantel,
            'name' => $validated['nombre'],
            'telefono' => $validated['telefono'],
        ]);

        return redirect()->route('moderador.inicio', ['tab' => 'maestros'])
            ->with('success', 'Maestro registrado exitosamente.')
            ->with('tab', 'maestros');
    }

    /**
     * Update an existing maestro.
     */
    public function updateMaestro(Request $request, Maestro $maestro)
    {
        // Ensure the moderator can only edit maestros from their plantel
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        if ($maestro->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para editar este maestro.');
        }

        // Validate the request
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'correo' => 'required|email|unique:users,email,' . $maestro->user->id,
            'contrasena' => 'nullable|string|min:6|confirmed',
            'id_institucion' => 'required|exists:instituciones,id',
        ]);

        // Ensure the selected institucion belongs to the same plantel
        $institucion = Institucion::findOrFail($validated['id_institucion']);
        if ($institucion->id_plantel !== $moderador->id_plantel) {
            abort(403, 'La institución seleccionada no pertenece a tu plantel.');
        }

        // Update the user
        $userData = [
            'email' => $validated['correo'],
        ];
        if (!empty($validated['contrasena'])) {
            $userData['password'] = Hash::make($validated['contrasena']);
        }
        $maestro->user->update($userData);

        // Update the maestro
        $maestro->update([
            'name' => $validated['nombre'],
            'telefono' => $validated['telefono'],
            'id_institucion' => $validated['id_institucion'],
        ]);

        return redirect()->route('moderador.inicio', ['tab' => 'maestros'])
            ->with('success', 'Maestro actualizado exitosamente.')
            ->with('tab', 'maestros');
    }

    /**
     * Delete an existing maestro and its associated user.
     */
    public function deleteMaestro(Maestro $maestro)
    {
        // Ensure the moderator can only delete maestros from their plantel
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        if ($maestro->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para eliminar este maestro.');
        }

        // Delete the associated user
        $maestro->user->delete();

        // Delete the maestro
        $maestro->delete();

        return redirect()->route('moderador.inicio', ['tab' => 'maestros'])
            ->with('success', 'Maestro eliminado exitosamente.')
            ->with('tab', 'maestros');
    }

    /**
     * Register a new especialidad.
     */
    public function registerEspecialidad(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'id_institucion' => 'required|exists:instituciones,id',
        ]);

        // Get the current moderator's id_plantel
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();

        // Ensure the selected institucion belongs to the same plantel
        $institucion = Institucion::findOrFail($validated['id_institucion']);
        if ($institucion->id_plantel !== $moderador->id_plantel) {
            abort(403, 'La institución seleccionada no pertenece a tu plantel.');
        }

        // Create the especialidad
        Especialidad::create([
            'name' => $validated['nombre'],
            'id_plantel' => $moderador->id_plantel,
            'id_institucion' => $validated['id_institucion'],
        ]);

        return redirect()->route('moderador.inicio', ['tab' => 'especialidades'])
            ->with('success', 'Especialidad registrada exitosamente.')
            ->with('tab', 'especialidades');
    }

    /**
     * Update an existing especialidad.
     */
    public function updateEspecialidad(Request $request, Especialidad $especialidad)
    {
        // Ensure the moderator can only edit especialidades from their plantel
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        if ($especialidad->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para editar esta especialidad.');
        }

        // Validate the request
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'id_institucion' => 'required|exists:instituciones,id',
        ]);

        // Ensure the selected institucion belongs to the same plantel
        $institucion = Institucion::findOrFail($validated['id_institucion']);
        if ($institucion->id_plantel !== $moderador->id_plantel) {
            abort(403, 'La institución seleccionada no pertenece a tu plantel.');
        }

        // Update the especialidad
        $especialidad->update([
            'name' => $validated['nombre'],
            'id_institucion' => $validated['id_institucion'],
        ]);

        return redirect()->route('moderador.inicio', ['tab' => 'especialidades'])
            ->with('success', 'Especialidad actualizada exitosamente.')
            ->with('tab', 'especialidades');
    }

    /**
     * Delete an existing especialidad.
     */
    public function deleteEspecialidad(Especialidad $especialidad)
    {
        // Ensure the moderator can only delete especialidades from their plantel
        $moderador = Moderador::where('id_user', Auth::id())->firstOrFail();
        if ($especialidad->id_plantel !== $moderador->id_plantel) {
            abort(403, 'No autorizado para eliminar esta especialidad.');
        }

        // Delete the especialidad
        $especialidad->delete();

        return redirect()->route('moderador.inicio', ['tab' => 'especialidades'])
            ->with('success', 'Especialidad eliminada exitosamente.')
            ->with('tab', 'especialidades');
    }
}