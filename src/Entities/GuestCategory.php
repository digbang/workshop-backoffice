<?php

namespace WorkshopBackoffice\Entities;

use Cake\Chronos\Chronos;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use WorkshopBackoffice\Payloads\GuestCategoryPayload;
use WorkshopBackoffice\Utils\Timestampable;

class GuestCategory
{
    use Timestampable;

    private UuidInterface $id;
    private string $name;

    public function __construct(GuestCategoryPayload $payload)
    {
        $this->id = Uuid::uuid4();
        $this->name = $payload->name();
        $this->createdAt = Chronos::now();
        $this->updatedAt = Chronos::now();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function update(GuestCategoryPayload $payload): void
    {
        $this->name = $payload->name();
        $this->updatedAt = Chronos::now();
    }
}
