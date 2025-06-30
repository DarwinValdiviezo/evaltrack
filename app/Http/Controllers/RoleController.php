<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = \App\Models\Role::on('pgsql')->get();
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'not_regex:/^[0-9]+$/', 'min:3'],
        ]);
        $predefinidos = ['Administrador', 'Gestor de Talento Humano', 'Empleado'];
        if (in_array($request->name, $predefinidos)) {
            return back()->with('error', 'No puedes crear un rol con ese nombre.');
        }
        $exists = \App\Models\Role::on('pgsql')->where('name', $request->name)->whereNull('deleted_at')->exists();
        if ($exists) {
            return back()->with('error', 'El nombre del rol ya existe.');
        }
        $role = new \App\Models\Role();
        $role->setConnection('pgsql');
        $role->name = $request->name;
        $role->guard_name = 'web';
        $role->save();
        return redirect()->route('roles.index')->with('success', 'Rol creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $role = \App\Models\Role::on('pgsql')->findOrFail($id);
        return view('roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $role = \App\Models\Role::on('pgsql')->findOrFail($id);
        $request->validate([
            'name' => ['required', 'string', 'not_regex:/^[0-9]+$/', 'min:3'],
        ]);
        $predefinidos = ['Administrador', 'Gestor de Talento Humano', 'Empleado'];
        if (in_array($request->name, $predefinidos)) {
            return back()->with('error', 'No puedes usar ese nombre para el rol.');
        }
        $exists = \App\Models\Role::on('pgsql')->where('name', $request->name)->where('id', '!=', $role->id)->whereNull('deleted_at')->exists();
        if ($exists) {
            return back()->with('error', 'El nombre del rol ya existe.');
        }
        $role->name = $request->name;
        $role->save();
        return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $request->validate([
            'admin_password' => 'required',
        ]);
        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return back()->with('error', 'Contraseña incorrecta.');
        }
        $role = \App\Models\Role::on('pgsql')->findOrFail($id);
        $predefinidos = ['Administrador', 'Gestor de Talento Humano', 'Empleado'];
        if (in_array($role->name, $predefinidos)) {
            return back()->with('error', 'No puedes borrar los roles predefinidos.');
        }
        $role->forceDelete();
        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }

    public function trashed()
    {
        $roles = \App\Models\Role::on('pgsql')->onlyTrashed()->get();
        return view('roles.trashed', compact('roles'));
    }

    public function restore($id)
    {
        $role = \App\Models\Role::on('pgsql')->withTrashed()->findOrFail($id);
        $role->restore();
        return redirect()->route('roles.trashed')->with('success', 'Rol restaurado correctamente.');
    }

    public function forceDelete(Request $request, $id)
    {
        $request->validate([
            'admin_password' => 'required',
        ]);
        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return back()->with('error', 'Contraseña incorrecta.');
        }
        $role = \App\Models\Role::on('pgsql')->withTrashed()->findOrFail($id);
        $role->forceDelete();
        return redirect()->route('roles.trashed')->with('success', 'Rol eliminado definitivamente.');
    }
}
