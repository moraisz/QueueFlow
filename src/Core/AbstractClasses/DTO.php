<?php

namespace Src\Core\AbstractClasses;

use Src\Core\Request;

abstract class DTO
{
    abstract public static function fromRequest(Request $request): self;

    // commented out to avoid errors in other DTOs that don't have an entity yet,
    // but should be implemented when the entity is created
    /* abstract public static function fromEntity($entity): self; */
}
