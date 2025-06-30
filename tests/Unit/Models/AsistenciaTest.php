<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Asistencia;

class AsistenciaTest extends TestCase
{
    public function test_asistencia_model_exists()
    {
        $this->assertTrue(class_exists(Asistencia::class));
    }

    public function test_asistencia_model_has_fillable_fields()
    {
        $asistencia = new Asistencia();
        $fillable = $asistencia->getFillable();
        
        $this->assertContains('evento_id', $fillable);
        $this->assertContains('empleado_id', $fillable);
        $this->assertContains('fecha_asistencia', $fillable);
        $this->assertContains('hora_asistencia', $fillable);
        $this->assertContains('asistio', $fillable);
        $this->assertContains('status', $fillable);
    }

    public function test_asistencia_model_has_connection()
    {
        $asistencia = new Asistencia();
        $this->assertEquals('mysql_business', $asistencia->getConnectionName());
    }
} 