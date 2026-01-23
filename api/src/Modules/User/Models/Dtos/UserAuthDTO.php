<?php 

namespace Modules\User\Models\Dtos;

class UserAuthDTO {

    /**
     * @param int $id ID único del usuario
     * @param string $name Nombre del usuario
     * @param string $last_name Apellido del usuario
     * @param string $dni Documento Nacional de Identidad
     * @param string $nickname Nombre de usuario único (para login)
     * @param string $phone Teléfono de contacto
     * @param string $email Email del usuario
     * @param int $level Nivel de acceso jerárquico (0=superadmin, 1=admin, 2=user, etc.)
     * @param array $offices Lista de oficinas asignadas
     *                          Estructura: ['ids' => [1,5,8], 'names' => ['create','edit','delete']]
     * @param array $permission Permisos del usuario
     *                          Estructura: ['ids' => [1,5,8], 'names' => ['create','edit','delete']]
     */
    public function __construct(
        public readonly int    $id,
        public readonly string $name,
        public readonly string $last_name,
        public readonly string $dni, 
        public readonly string $nickname,
        public readonly ?string $phone,
        public readonly ?string $email,
        public readonly int    $level,
        public readonly int    $token_version, 
        public readonly array  $offices,
        public readonly array  $permissions
    ) {}
}