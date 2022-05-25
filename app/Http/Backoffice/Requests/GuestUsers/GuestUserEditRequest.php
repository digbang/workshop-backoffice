<?php

namespace App\Http\Backoffice\Requests\GuestUsers;

use Cake\Chronos\Chronos;
use Cake\Chronos\ChronosInterface;
use Digbang\Security\Users\ValueObjects\Name;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use WorkshopBackoffice\Enumerables\Country;
use WorkshopBackoffice\Payloads\GuestUserPayload;
use WorkshopBackoffice\Repositories\GuestCategoryRepository;

class GuestUserEditRequest implements GuestUserPayload
{
    public const ROUTE_PARAM_ID = 'guestUserId';
    public const FIST_NAME = 'firstName';
    public const LAST_NAME = 'lastName';
    public const DESCRIPTION = 'description';
    public const BIRTHDAY = 'birthday';
    public const ADDRESS = 'address';
    public const ACTIVE = 'active';
    public const ADMISSION_DATE = 'admissionDate';
    public const WISH_TO_BE_CONTACTED = 'wishToBeContacted';
    public const CAN_BE_EDITED = 'canBeEdited';
    public const PHONE_NUMBER = 'phoneNumber';
    public const COUNTRY = 'country';
    public const CATEGORIES = 'categories';
    public const COMMENTS = 'comments';

    private Request $request;
    private GuestCategoryRepository $guestCategoryRepository;

    public function __construct(
        Request $request,
        GuestCategoryRepository $guestCategoryRepository
    ) {
        $this->request = $request;
        $this->guestCategoryRepository = $guestCategoryRepository;
    }

    public function id(): UuidInterface
    {
        /** @var Route $route */
        $route = $this->request->route();

        $id = $route->parameter(self::ROUTE_PARAM_ID);

        return Uuid::fromString($id);
    }

    public function name(): Name
    {
        return new Name($this->request->get(self::FIST_NAME), $this->request->get(self::LAST_NAME));
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
        return $this->request->get(self::ADDRESS);
    }

    public function isActivated(): bool
    {
        return $this->request->get(self::ACTIVE);
    }

    public function admissionDate(): ChronosInterface
    {
        return new Chronos($this->request->get(self::ADMISSION_DATE));
    }

    public function wishToBeContacted(): bool
    {
        return (bool) $this->request->get(self::WISH_TO_BE_CONTACTED);
    }

    public function phoneNumber(): int
    {
        return $this->request->get(self::PHONE_NUMBER);
    }

    public function comments(): string
    {
        return $this->request->get(self::COMMENTS);
    }

    public function canBeEdited(): bool
    {
        return (bool) $this->request->get(self::CAN_BE_EDITED);
    }

    public function categories(): array
    {
        $categories = [];
        foreach ($this->request->get(self::CATEGORIES) as $categoryId) {
            $categories[] = array_first($this->guestCategoryRepository->findBy(['id' => $categoryId]));
        }

        return $categories;
    }

    public function validate(): array
    {
        $rules = [
            self::FIST_NAME => 'required',
            self::LAST_NAME => 'required',
            self::DESCRIPTION => 'required',
            self::BIRTHDAY => 'required',
            self::ADDRESS => 'required',
            self::ACTIVE => 'required',
            self::ADMISSION_DATE => 'required',
            self::WISH_TO_BE_CONTACTED => 'boolean',
            self::PHONE_NUMBER => 'required',
            self::COMMENTS => 'required',
            self::COUNTRY => 'required|in:' . implode(',', Country::getAllValues()),
        ];

        return $this->request->validate($rules);
    }
}
