<?php

namespace WorkshopBackoffice\Entities;

use Cake\Chronos\Chronos;
use Cake\Chronos\ChronosInterface;
use Digbang\Security\Users\ValueObjects\Name;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private string $description;
    private ChronosInterface $birthdate;
    private string $address;
    private bool $isActivated;
    private ChronosInterface $admissionDate;
    private bool $wishToBeContacted;
    private bool $canBeEdited;
    private int $phoneNumber;
    private string $comments;
    private Collection $categories;

    public function __construct(GuestUserPayload $payload)
    {
        $this->id = Uuid::uuid4();
        $this->name = $payload->name();
        $this->country = $payload->country();
        $this->description = $payload->description();
        $this->birthdate = $payload->birthdate();
        $this->address = $payload->address();
        $this->isActivated = $payload->isActivated();
        $this->admissionDate = $payload->admissionDate();
        $this->wishToBeContacted = $payload->wishToBeContacted();
        $this->phoneNumber = $payload->phoneNumber();
        $this->comments = $payload->comments();
        $this->canBeEdited = $payload->canBeEdited();
        $this->categories = new ArrayCollection();

        foreach ($payload->categories() as $category) {
            $this->categories->add($category);
        }

        $this->createdAt = Chronos::now();
        $this->updatedAt = Chronos::now();
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getBirthdate(): ChronosInterface
    {
        return $this->birthdate;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function isActivated(): bool
    {
        return $this->isActivated;
    }

    public function getAdmissionDate(): ChronosInterface
    {
        return $this->admissionDate;
    }

    public function isWishToBeContacted(): bool
    {
        return $this->wishToBeContacted;
    }

    public function getPhoneNumber(): int
    {
        return $this->phoneNumber;
    }

    public function getComments(): string
    {
        return $this->comments;
    }

    public function canBeEdited(): bool
    {
        return $this->canBeEdited;
    }

    public function update(GuestUserPayload $payload): void
    {
        $this->name = $payload->name();
        $this->country = $payload->country();

        $this->categories->clear();
        foreach ($payload->categories() as $category) {
            $this->categories->add($category);
        }
        $this->updatedAt = Chronos::now();
    }

    public function getCategories(): array
    {
        return $this->categories->toArray();
    }
}
