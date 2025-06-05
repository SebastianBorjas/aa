<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entrega extends Model
{
    public $timestamps = true;

    protected $table = 'entregas';

    protected $fillable = [
        'id_subtema',
        'id_alumno',
        'contenido',
        'rutas',
        'estado',
        'rce',
        'rcm',
        'vce',
        'vcm',
    ];

    protected $casts = [
        'rutas' => 'array',
    ];

    public function subtema(): BelongsTo
    {
        return $this->belongsTo(Subtema::class, 'id_subtema');
    }

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }
}
