<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subtema extends Model
{
    public $timestamps = true;

    protected $table = 'subtemas';

    protected $fillable = [
        'id_tema',
        'nombre',
        'descripcion',
        'rutas',
    ];

    protected $casts = [
        'rutas' => 'array',
    ];

    public function tema(): BelongsTo
    {
        return $this->belongsTo(Tema::class, 'id_tema');
    }

    public function entregas(): HasMany
    {
        return $this->hasMany(Entrega::class, 'id_subtema');
    }
}