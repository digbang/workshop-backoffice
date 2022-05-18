<?php

namespace WorkshopBackoffice\Entities;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use WorkshopBackoffice\Utils\Timestampable;

class GuestCategory
{
    use Timestampable;

    private UuidInterface $id;
    private string $name;

    public function __construct(string $name)
    {
        $this->id = Uuid::uuid4();
        $this->name = $name;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
