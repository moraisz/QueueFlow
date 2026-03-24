<?php

namespace Src\Contracts\DTOs\Customer;

use Src\Core\AbstractClasses\DTO;
use Src\Core\Request;

class CustomerCreateDTO extends DTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $priority,
        public readonly string $type,
        public readonly string $status,
        public readonly ?string $email,
        public readonly ?string $telephone
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: (int) $request->getBody('name'),
            priority: $request->getBody('priority'),
            type: $request->getBody('type'),
            status: $request->getBody('status'),
            email: $request->getBody('email'),
            telephone: $request->getBody('telephone')
        );
    }

    public static function fromEntity(object $Entity): self
    {
        return new self(
            name: $Entity->name,
            priority: $Entity->priority,
            type: $Entity->type,
            status: $Entity->status,
            email: $Entity->email,
            telephone: $Entity->telephone
        );
    }
}
