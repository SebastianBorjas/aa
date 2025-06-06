<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Lista;
class Alumno extends Model
{
    public $timestamps = true;

    protected $table = 'alumnos';

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
        'id_institucion',
        'id_plan',
    ];

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function plantel(): BelongsTo
    {
        return $this->belongsTo(Plantel::class, 'id_plantel');
    }

    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class, 'id_especialidad');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function maestro(): BelongsTo
    {
        return $this->belongsTo(Maestro::class, 'id_maestro');
    }

    public function institucion(): BelongsTo
    {
        return $this->belongsTo(Institucion::class, 'id_institucion');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }
    public function entregas(): HasMany
    {
        return $this->hasMany(Entrega::class, 'id_alumno');
    }
    public function listas(): HasMany
    {
        return $this->hasMany(Lista::class, 'id_alumno');
    }
}