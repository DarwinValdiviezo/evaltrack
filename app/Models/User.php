<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $connection = 'pgsql';

    protected $table = 'users';

    protected $fillable = [
        'username',
        'email',
        'password',
        'nombre',
        'apellido',
        'cedula',
        'telefono',
        'fecha_nacimiento',
        'cargo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    // Mutator para hashear la contraseña
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    // Relación con empleados
    public function empleado()
    {
        return $this->hasOne(Employee::class);
    }
}
