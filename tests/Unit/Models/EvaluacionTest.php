<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Evaluacion;

class EvaluacionTest extends TestCase
{
    public function test_evaluacion_model_exists()
    {
        $this->assertTrue(class_exists(Evaluacion::class));
    }

    public function test_evaluacion_model_has_fillable_fields()
    {
        $evaluacion = new Evaluacion();
        $fillable = $evaluacion->getFillable();
        
        $this->assertContains('evento_id', $fillable);
        $this->assertContains('empleado_id', $fillable);
        $this->assertContains('status', $fillable);
        $this->assertContains('respuestas', $fillable);
        $this->assertContains('nota', $fillable);
        $this->assertContains('comentarios', $fillable);
        $this->assertContains('preguntas', $fillable);
    }

    public function test_evaluacion_model_has_connection()
    {
        $evaluacion = new Evaluacion();
        $this->assertEquals('mysql_business', $evaluacion->getConnectionName());
    }

    public function test_evaluacion_model_has_casts()
    {
        $evaluacion = new Evaluacion();
        $casts = $evaluacion->getCasts();
        
        $this->assertArrayHasKey('respuestas', $casts);
        $this->assertArrayHasKey('preguntas', $casts);
    }
} 