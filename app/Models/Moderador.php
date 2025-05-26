<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Moderador extends Model
{
    public $timestamps = false;
    protected $table = 'moderadores';
    protected $fillable = ['id_user', 'id_plantel', 'name']; // Adiciona 'name' ao fillable

    /**
     * Un moderador pertenece a un usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'id_user');
    }

    /**
     * Un moderador pertenece a un plantel
     */
    public function plantel(): BelongsTo
    {
        return $this->belongsTo(Plantel::class, 'id_plantel');
    }
}