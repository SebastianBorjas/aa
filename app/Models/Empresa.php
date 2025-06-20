<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Empresa extends Model
{
    // Enable timestamps since the schema includes created_at/updated_at
    public $timestamps = true;

    // Explicitly define the table name (optional, as Laravel would infer 'empresas')
    protected $table = 'empresas';

    // Columns that are mass assignable
    protected $fillable = [
        'id_user',
        'id_plantel',
        'name',
        'responsable',
        'telefono',
    ];

    /**
     * An empresa belongs to a user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * An empresa belongs to a plantel
     */
    public function plantel(): BelongsTo
    {
        return $this->belongsTo(Plantel::class, 'id_plantel');
    }
}