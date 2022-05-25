<?php

namespace WorkshopBackoffice\Payloads;

use Cake\Chronos\ChronosInterface;
use Digbang\Security\Users\ValueObjects\Name;
use WorkshopBackoffice\Enumerables\Country;

interface GuestUserPayload
{
    public function name(): Name;

    public function country(): Country;

    public function description(): string;

    public function birthdate(): ChronosInterface;

    public function address(): string;

    public function isActivated(): bool;

    public function admissionDate(): ChronosInterface;

    public function wishToBeContacted(): bool;

    public function canBeEdited(): bool;

    public function phoneNumber(): int;

    public function comments(): string;

    public function categories(): array;
}
