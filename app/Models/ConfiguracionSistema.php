<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionSistema extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'configuracion_sistema';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
    ];

    /**
     * No timestamps for this model.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Set the primary key if it's not 'id'.
     *
     * @var string
     */
    protected $primaryKey = 'clave';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
}
