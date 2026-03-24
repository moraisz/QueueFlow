<?php

namespace Src\Contracts\DTOs\Customer;

use Src\Application\Entities\CustomerEntity;
use Src\Contracts\DTOs\DTO;
use Src\Core\Request;

class CustomerResponseDTO extends DTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $email,
        public readonly ?string $telephone,
        public readonly string $priority,
        public readonly string $type,
        public readonly string $status
    ) {
    }

    public static function fromEntity(CustomerEntity $entity): self
    {
        return new self(
            id: $entity->getId(),
            name: $entity->getName(),
            email: $entity->getEmail(),
            telephone: $entity->getTelephone(),
            priority: $entity->getPriority(),
            type: $entity->getType(),
            status: $entity->getStatus()
        );
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            id: (int) $request->getBody('id'),
            name: $request->getBody('name'),
            email: $request->getBody('email'),
            telephone: $request->getBody('telephone'),
            priority: $request->getBody('priority'),
            type: $request->getBody('type'),
            status: $request->getBody('status')
        );
    }
}
