<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    // Datos principales en MySQL
    protected $connection = 'mysql_business';
    protected $table      = 'employees';

    protected $fillable = [
        'user_id',
        'nombre',
        'apellido',
        'cedula',
        'email',
        'telefono',
        'fecha_nacimiento',
        'cargo',
        'estado',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    // Relación con el usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relación con asistencias
    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class, 'empleado_id');
    }

    // Relación con evaluaciones
    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class, 'empleado_id');
    }

    // Accessor para nombre completo
    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    // Scope para empleados activos (que tienen usuario asociado)
    public function scopeActivos($query)
    {
        return $query->whereNotNull('user_id');
    }
}
