<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Rbac\Models\Permission;
use Modules\Rbac\Models\Role;

class Rbac extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Permisos Globales
        $this->addPermission('*', 'wildcard', 'Permission for all actions (exclusively for sysadmin)');
        $this->addPermission('offices.manage', 'Oficinas y Locales', 'Administrar Oficinas y locales'); 
        $this->addPermission('rbac.role', 'Roles y Permisos', 'Administrar Roles y Permisos');
        // 2. Módulos Específicos
        $this->seedPostulantes();
        $this->seedUsers();
        $this->seedRbac();
    }

    protected function seedPostulantes(): void
    {
        $permiso = $this->addPermission(
            'p.postulante', 
            'Aplicante', 
            'Permisos para aplicar a una convocatoria'
        );

        $this->addRole(
            'p.postulante', 
            'Role Aplicante', 
            'Usuario con permisos para aplicar a una convocatoria', 
            [$permiso->id]
        );
    }

    protected function seedUsers(): void
    {
        $this->addPermission('user.list',   'Usuario Listar', 'Permite listar usuarios');
        $this->addPermission('user.create', 'Usuario Crear',  'Crear usuarios');
        $this->addPermission('users.edit',  'Usuario Editar', 'Permite editar usuarios');
    }

    protected function seedRbac(): void
    {
        $this->addPermission('rbac.role', 'Administrar Roles', 'Administrar roles');
    }


    private function addPermission(string $name, string $display, string $description): Permission
    {
        return Permission::firstOrCreate(
            ['name' => $name],
            [
                'display_name' => $display,
                'description'  => $description,
            ]
        );
    }

    private function addRole(string $name, string $display, string $description, array $permissions = []): Role
    {
        $role = Role::firstOrCreate(
            ['name' => $name],
            [
                'display_name' => $display,
                'description'  => $description,
            ]
        );

        if (!empty($permissions)) {
            $role->permissions()->sync($permissions);
        }

        return $role;
    }
}