<?php 

namespace Modules\Shared\Applications\Dtos;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as Eloquent;

class CollectionGenericDTO {
    /**
     * @param Collection|Eloquent<int, array{id:int, group:string, value:string}> $data
     */
    public function __construct(
        public Collection|Eloquent $items
    ) {}
}