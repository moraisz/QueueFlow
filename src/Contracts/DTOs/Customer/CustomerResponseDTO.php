<?php

namespace Src\Contracts\DTOs\Customer;

use Src\Application\Entities\CustomerEntity;

class CustomerResponseDTO {
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $email,
        public readonly ?string $telephone,
        public readonly string $priority,
        public readonly string $type,
        public readonly string $status
    ) {}

    public static function fromEntity(CustomerEntity $entity): self {
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
}
