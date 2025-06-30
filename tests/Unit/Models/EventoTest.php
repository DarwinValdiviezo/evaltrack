<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Evento;

class EventoTest extends TestCase
{
    public function test_evento_model_exists()
    {
        $this->assertTrue(class_exists(Evento::class));
    }

    public function test_evento_model_has_fillable_fields()
    {
        $evento = new Evento();
        $fillable = $evento->getFillable();
        
        $this->assertContains('nombre', $fillable);
        $this->assertContains('descripcion', $fillable);
        $this->assertContains('fecha_evento', $fillable);
        $this->assertContains('tipo', $fillable);
        $this->assertContains('estado', $fillable);
    }

    public function test_evento_model_has_connection()
    {
        $evento = new Evento();
        $this->assertEquals('mysql_business', $evento->getConnectionName());
    }

    public function test_evento_model_has_casts()
    {
        $evento = new Evento();
        $casts = $evento->getCasts();
        
        $this->assertArrayHasKey('fecha_evento', $casts);
        $this->assertArrayHasKey('hora_inicio', $casts);
        $this->assertArrayHasKey('hora_fin', $casts);
    }
} 