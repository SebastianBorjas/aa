<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alumno extends Model
{
    // Enable timestamps since the schema includes created_at/updated_at
    public $timestamps = true;

    // Explicitly define the table name (optional, as Laravel would infer 'alumnos')
    protected $table = 'alumnos';

    // Columns that are mass assignable
    protected $fillable = [
        'id_user',
        'id_plantel',
        'id_especialidad',
        'name',
        'telefono',
        'telefono_emergencia',
        'lunes',
        'martes',
        'miercoles',
        'jueves',
        'viernes',
        'sabado',
        'domingo',
        'fecha_inicio',
        'fecha_termino',
        'id_empresa',
        'id_maestro',
    ];

    // Cast attributes to specific types
    protected $casts = [
        'lunes' => 'boolean',
        'martes' => 'boolean',
        'miercoles' => 'boolean',
        'jueves' => 'boolean',
        'viernes' => 'boolean',
        'sabado' => 'boolean',
        'domingo' => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_termino' => 'date',
    ];

    /**
     * An alumno belongs to a user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * An alumno belongs to a plantel
     */
    public function plantel(): BelongsTo
    {
        return $this->belongsTo(Plantel::class, 'id_plantel');
    }

    /**
     * An alumno belongs to an especialidad
     */
    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class, 'id_especialidad');
    }

    /**
     * An alumno belongs to an empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    /**
     * An alumno belongs to a maestro
     */
    public function maestro(): BelongsTo
    {
        return $this->belongsTo(Maestro::class, 'id_maestro');
    }
}