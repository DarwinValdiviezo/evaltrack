<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluacion extends Model
{
    protected $connection = 'mysql_business';
    protected $table      = 'evaluaciones';
    protected $primaryKey = 'id';

    protected $fillable = [
        'evento_id',
        'empleado_id',
        'status',
        'respuestas',
        'nota',
        'comentarios',
        'preguntas',
    ];

    protected $casts = [
        'respuestas' => 'array',
        'preguntas' => 'array',
    ];

    // Relación con el evento
    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    // Relación con el empleado
    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Relación con el usuario que evaluó
    public function evaluadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluado_por');
    }

    // Scope para evaluaciones pendientes
    public function scopePendientes($query)
    {
        return $query->where('status', 'Pendiente');
    }

    // Scope para evaluaciones completadas
    public function scopeCompletadas($query)
    {
        return $query->where('status', 'Completada');
    }

    // Scope para evaluaciones calificadas
    public function scopeCalificadas($query)
    {
        return $query->where('status', 'Calificada');
    }

    // Método para obtener el nombre del parámetro de ruta
    public function getRouteKeyName()
    {
        return 'id';
    }
}
