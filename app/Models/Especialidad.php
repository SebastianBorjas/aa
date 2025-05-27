<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Especialidad extends Model
{
    // Enable timestamps since the schema includes created_at/updated_at
    public $timestamps = true;

    // Explicitly define the table name (optional, as Laravel would infer 'especialidades')
    protected $table = 'especialidades';

    // Columns that are mass assignable
    protected $fillable = [
        'name',
        'id_plantel',
        'id_institucion',
    ];

    /**
     * An especialidad belongs to a plantel
     */
    public function plantel(): BelongsTo
    {
        return $this->belongsTo(Plantel::class, 'id_plantel');
    }

    /**
     * An especialidad belongs to an institucion
     */
    public function institucion(): BelongsTo
    {
        return $this->belongsTo(Institucion::class, 'id_institucion');
    }
}