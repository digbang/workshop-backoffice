<?php

namespace App\Http\Backoffice\Requests\GuestUsers;

use Cake\Chronos\Chronos;
use Cake\Chronos\ChronosInterface;
use Digbang\Security\Users\ValueObjects\Name;
use Illuminate\Http\Request;
use WorkshopBackoffice\Enumerables\Country;
use WorkshopBackoffice\Payloads\GuestUserPayload;

class GuestUserCreateRequest implements GuestUserPayload
{
    public const FIRST_NAME = 'firstName';
    public const LAST_NAME = 'lastName';
    public const DESCRIPTION = 'description';
    public const BIRTHDAY = 'birthday';
    public const ADDRESS = 'address';
    public const COUNTRY = 'country';
    public const ADMISSION_DATE = 'admissionDate';
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function name(): Name
    {
        return new Name($this->request->get(self::FIRST_NAME), $this->request->get(self::LAST_NAME));
    }

    public function country(): Country
    {
        return Country::fromString($this->request->get(self::COUNTRY));
    }

    public function description(): string
    {
        return $this->request->get(self::DESCRIPTION);
    }

    public function birthdate(): ChronosInterface
    {
        return new Chronos($this->request->get(self::BIRTHDAY));
    }

    public function address(): string
    {
        return $this->request->get(self::DESCRIPTION);
    }

    public function isActivated(): bool
    {
        return false;
    }

    public function admissionDate(): ChronosInterface
    {
        return new Chronos($this->request->get(self::ADMISSION_DATE));
    }

    public function wishToBeContacted(): bool
    {
        return false;
    }

    public function canBeEdited(): bool
    {
        return false;
    }

    public function phoneNumber(): int
    {
        return 0;
    }

    public function comments(): string
    {
        return '';
    }

    public function categories(): array
    {
        return [];
    }
}
