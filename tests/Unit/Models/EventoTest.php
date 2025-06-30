<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear un usuario para asociar al evento
        $this->user = User::create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_evento_can_be_created()
    {
        $evento = Evento::create([
            'nombre' => 'Capacitación Power BI',
            'descripcion' => 'Aprende Power BI',
            'fecha_evento' => '2025-07-15',
            'hora_inicio' => '09:00',
            'hora_fin' => '17:00',
            'tipo' => 'Capacitación',
            'estado' => 'Programado',
            'creado_por' => $this->user->id,
        ]);

        $this->assertDatabaseHas('eventos', [
            'nombre' => 'Capacitación Power BI',
            'tipo' => 'Capacitación',
        ]);
    }

    public function test_evento_has_activos_scope()
    {
        Evento::create([
            'nombre' => 'Evento Activo',
            'descripcion' => 'Descripción',
            'fecha_evento' => '2025-07-15',
            'hora_inicio' => '09:00',
            'hora_fin' => '17:00',
            'tipo' => 'Capacitación',
            'estado' => 'Programado',
            'creado_por' => $this->user->id,
        ]);

        Evento::create([
            'nombre' => 'Evento Cancelado',
            'descripcion' => 'Descripción',
            'fecha_evento' => '2025-07-15',
            'hora_inicio' => '09:00',
            'hora_fin' => '17:00',
            'tipo' => 'Capacitación',
            'estado' => 'Cancelado',
            'creado_por' => $this->user->id,
        ]);

        $activos = Evento::activos()->get();
        $this->assertEquals(1, $activos->count());
        $this->assertEquals('Evento Activo', $activos->first()->nombre);
    }

    public function test_evento_belongs_to_creator()
    {
        $evento = Evento::create([
            'nombre' => 'Capacitación Power BI',
            'descripcion' => 'Aprende Power BI',
            'fecha_evento' => '2025-07-15',
            'hora_inicio' => '09:00',
            'hora_fin' => '17:00',
            'tipo' => 'Capacitación',
            'estado' => 'Programado',
            'creado_por' => $this->user->id,
        ]);

        $this->assertInstanceOf(User::class, $evento->creadoPor);
        $this->assertEquals($this->user->id, $evento->creadoPor->id);
    }

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