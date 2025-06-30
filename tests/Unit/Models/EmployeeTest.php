<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Employee;

class EmployeeTest extends TestCase
{
    public function test_employee_model_exists()
    {
        $this->assertTrue(class_exists(Employee::class));
    }

    public function test_employee_model_has_fillable_fields()
    {
        $employee = new Employee();
        $fillable = $employee->getFillable();
        
        $this->assertContains('user_id', $fillable);
        $this->assertContains('nombre', $fillable);
        $this->assertContains('apellido', $fillable);
        $this->assertContains('cedula', $fillable);
        $this->assertContains('email', $fillable);
        $this->assertContains('cargo', $fillable);
        $this->assertContains('estado', $fillable);
    }

    public function test_employee_model_has_connection()
    {
        $employee = new Employee();
        $this->assertEquals('mysql_business', $employee->getConnectionName());
    }

    public function test_employee_model_has_casts()
    {
        $employee = new Employee();
        $casts = $employee->getCasts();
        
        $this->assertArrayHasKey('fecha_nacimiento', $casts);
    }
} 