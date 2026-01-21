<?php

namespace Src\Contracts\DTOs\Customer;

class CustomerCreateDTO {
    public function __construct(
        public readonly string $name,
        public readonly string $priority,
        public readonly string $type,
        public readonly string $status,
        public readonly ?string $email,
        public readonly ?string $telephone
    ) {}
}
