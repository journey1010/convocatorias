<?php
namespace Modules\User\Models\Dtos;

class UserAuthDTOBuilder
{
    private ?array $offices = null;
    private ?array $permissions = null;

    public function __construct(
        private int $id,
        private string $name,
        private string $last_name,
        private string $dni,
        private string $nickname,
        private string $phone,
        private string $email,
        private int $level
    ) {}

    public function withOffices(array $offices): self
    {
        $this->offices = $offices;
        return $this;
    }

    public function withPermissions(array $permissions): self
    {
        $this->permissions = $permissions;
        return $this;
    }

    public function build(): UserAuthDTO
    {
        return new UserAuthDTO(
            id: $this->id,
            name: $this->name,
            last_name: $this->last_name,
            dni: $this->dni,
            nickname: $this->nickname,
            phone: $this->phone,
            email: $this->email,
            level: $this->level,
            permissions: $this->permissions ?? [],
            offices: $this->offices ?? []
        );
    }
}