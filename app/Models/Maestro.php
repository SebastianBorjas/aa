<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maestro extends Model
{
    // Enable timestamps since the schema includes created_at/updated_at
    public $timestamps = true;

    // Explicitly define the table name (optional, as Laravel would infer 'maestros')
    protected $table = 'maestros';

    // Columns that are mass assignable
    protected $fillable = [
        'id_user',
        'id_institucion',
        'id_plantel',
        'name',
        'telefono',
    ];

    /**
     * A maestro belongs to a user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * A maestro belongs to an institucion
     */
    public function institucion(): BelongsTo
    {
        return $this->belongsTo(Institucion::class, 'id_institucion');
    }

    /**
     * A maestro belongs to a plantel
     */
    public function plantel(): BelongsTo
    {
        return $this->belongsTo(Plantel::class, 'id_plantel');
    }
}