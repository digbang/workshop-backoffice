<?php

namespace App\Http\Backoffice\Requests\GuestCategories;

use Illuminate\Http\Request;
use WorkshopBackoffice\Payloads\GuestCategoryPayload;

class GuestCategoryCreateRequest implements GuestCategoryPayload
{
    public const NAME = 'name';

    private Request $request;

    public function __construct(
        Request $request
    ) {
        $this->request = $request;
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
