<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institucion extends Model
{
    // Enable timestamps since the schema includes created_at/updated_at
    public $timestamps = true;

    // Explicitly define the table name (optional, as Laravel would infer 'instituciones')
    protected $table = 'instituciones';

    // Columns that are mass assignable
    protected $fillable = [
        'id_user',
        'id_plantel',
        'name',
    ];

    /**
     * An institucion belongs to a user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * An institucion belongs to a plantel
     */
    public function plantel(): BelongsTo
    {
        return $this->belongsTo(Plantel::class, 'id_plantel');
    }

    /**
     * An institucion can have many maestros
     */
    public function maestros(): HasMany
    {
        return $this->hasMany(Maestro::class, 'id_institucion');
    }
}