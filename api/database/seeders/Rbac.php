<?php

namespace Database\Seeders;

Use Modules\Rbac\Models\{Permission, Role};
use Illuminate\Database\Seeder;

class Rbac extends Seeder
{
    public function run(): void
    {
        // wildcard
        $this->addPermission('*', 'wildcard', 'Permission for all actions (exclusively for sysadmin)');

        $this->addPermission('topic.manage', 'Temáticas Administrador', 'Permite administrar temáticas y sub-temáticas' );

        $this->addPermission('tipo_elemento.manage', 'Tipo Elemento Administrador', 'Permite crear, listar, actualizar los tipos de elementos');

        $this->users();
        $this->rbac();
    }

    private function addPermission(string $name, string $display, string $description)
    {
        Permission::firstOrCreate(
            ['name' => $name],
            [
                'display_name' => $display,
                'description' => $description
            ]
        );
    }

    public function users()
    {
        $this->addPermission('user.list',   'Usuario Listar', 'Permite listar usuarios');
        $this->addPermission('user.create', 'Usuario Crear',  'Crear usuarios');
        $this->addPermission('users.edit',  'Usuario Editar', 'Permite editar usuarios');
    }

    public function rbac()
    {
        $this->addPermission('rbac.role', 'Administrar Roles', 'Administrar roles');
    }
}