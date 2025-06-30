<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'asistencias';

    protected $fillable = [
        'evento_id',
        'empleado_id',
        'fecha_asistencia',
        'hora_asistencia',
        'asistio',
        'comentario',
        'status',
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

    // Scope para asistencias confirmadas
    public function scopeConfirmadas($query)
    {
        return $query->where('status', 'Confirmada');
    }

    // Scope para asistencias pendientes
    public function scopePendientes($query)
    {
        return $query->where('asistio', 'Pendiente');
    }
}
