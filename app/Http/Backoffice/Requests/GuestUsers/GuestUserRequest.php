<?php

namespace App\Http\Backoffice\Requests\GuestUsers;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class GuestUserRequest
{
    public const ROUTE_PARAM_ID = 'guestCategoryId';

    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function id(): UuidInterface
    {
        /** @var Route $route */
        $route = $this->request->route();

        $id = $route->parameter(self::ROUTE_PARAM_ID);

        return Uuid::fromString($id);
    }
}
