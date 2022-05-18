<?php

namespace WorkshopBackoffice\Entities;

use Digbang\Security\Users\ValueObjects\Name;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use WorkshopBackoffice\Enumerables\Country;
use WorkshopBackoffice\Payloads\GuestUserPayload;
use WorkshopBackoffice\Utils\Timestampable;

class GuestUser
{
    use Timestampable;

    private UuidInterface $id;
    private Name $name;
    private Country $country;

    public function __construct(GuestUserPayload $payload)
    {
        $this->id = Uuid::uuid4();
        $this->name = $payload->name();
        $this->country = $payload->country();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function update(GuestUserPayload $payload): void
    {
        $this->name = $payload->name();
        $this->country = $payload->country();
    }
}
