<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evento extends Model
{
    protected $connection = 'mysql_business';
    protected $table = 'eventos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_evento',
        'hora_inicio',
        'hora_fin',
        'lugar',
        'tipo',
        'estado',
        'creado_por',
    ];

    protected $casts = [
        'fecha_evento' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
    ];

    // Relación con el usuario que creó el evento
    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    // Relación con asistencias
    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class);
    }

    // Relación con evaluaciones
    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class);
    }

    // Scope para eventos activos
    public function scopeActivos($query)
    {
        return $query->whereIn('estado', ['Programado', 'En Curso']);
    }

    // Scope para eventos de capacitación
    public function scopeCapacitaciones($query)
    {
        return $query->where('tipo', 'Capacitación');
    }
}
