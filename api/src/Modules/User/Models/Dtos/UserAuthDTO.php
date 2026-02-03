<?php 

namespace Modules\User\Models\Dtos;

class UserAuthDTO implements \JsonSerializable {

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
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly int    $level,
        public readonly array  $offices,
        public readonly array  $permissions
    ) {}

    public function jsonSerialize(): array {
        return array_filter(get_object_vars($this), function ($value) {
            return !is_null($value);
        });
    }
}