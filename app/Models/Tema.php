<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tema extends Model
{
    public $timestamps = true;

    protected $table = 'temas';

    protected $fillable = [
        'id_plan',
        'nombre',
        'descripcion',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function subtemas(): HasMany
    {
        return $this->hasMany(Subtema::class, 'id_tema');
    }
}