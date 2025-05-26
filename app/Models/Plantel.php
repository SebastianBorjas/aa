<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plantel extends Model
{
    // Indica que no hay columnas created_at/updated_at
    public $timestamps = false;

    // Nombre de la tabla (por convención Laravel hubiera sido "plantels")
    protected $table = 'planteles';

    // Columns que se pueden asignar en masa
    protected $fillable = ['nombre'];

    /**
     * Un plantel puede tener muchos moderadores
     */
    public function moderadores(): HasMany
    {
        return $this->hasMany(Moderador::class, 'id_plantel');
    }
}
