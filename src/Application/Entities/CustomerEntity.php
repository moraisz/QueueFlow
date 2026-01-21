<?php

namespace Src\Application\Entities;

class CustomerEntity {
    private ?int $id = null;
    private string $name;
    private ?string $email;
    private ?string $telephone;
    private string $priority;
    private string $type;
    private string $status;

    public function __construct(
        string $name,
        string $priority,
        string $type,
        string $status,
        ?string $email = null,
        ?string $telephone = null,
        ?int $id = null
    ) {
        $this->name = $name;
        $this->priority = $priority;
        $this->type = $type;
        $this->status = $status;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->id = $id;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): ?string { return $this->email; }
    public function getTelephone(): ?string { return $this->telephone; }
    public function getPriority(): string { return $this->priority; }
    public function getType(): string { return $this->type; }
    public function getStatus(): string { return $this->status; }
}
