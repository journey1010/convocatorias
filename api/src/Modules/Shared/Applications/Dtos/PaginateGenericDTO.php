<?php 

namespace Modules\Shared\Applications\Dtos;

final class PaginateGenericDTO {
    
    public function __construct(
        public array $items,        
        public int $total,
    ) {}
}