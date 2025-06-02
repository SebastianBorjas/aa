<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    public $timestamps = true;

    protected $table = 'planes';

    protected $fillable = [
        'id_maestro',
        'nombre',
    ];

    public function maestro(): BelongsTo
    {
        return $this->belongsTo(Maestro::class, 'id_maestro');
    }

    public function temas(): HasMany
    {
        return $this->hasMany(Tema::class, 'id_plan');
    }
}