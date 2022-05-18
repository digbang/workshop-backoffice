<?php

namespace App\Http\Backoffice\Requests\GuestCategories;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use WorkshopBackoffice\Payloads\GuestCategoryPayload;

class GuestCategoryEditRequest implements GuestCategoryPayload
{
    public const ROUTE_PARAM_ID = 'guestCategoryId';
    public const NAME = 'name';

    private Request $request;

    public function __construct(
        Request $request
    ) {
        $this->request = $request;
    }

    public function id(): UuidInterface
    {
        /** @var Route $route */
        $route = $this->request->route();

        $id = $route->parameter(self::ROUTE_PARAM_ID);

        return Uuid::fromString($id);
    }

    public function name(): string
    {
        return $this->request->get(self::NAME);
    }

    public function validate(): array
    {
        $rules = [
            self::NAME => 'required',
        ];

        return $this->request->validate($rules);
    }
}
