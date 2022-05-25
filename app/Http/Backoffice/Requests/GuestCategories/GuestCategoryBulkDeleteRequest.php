<?php

namespace App\Http\Backoffice\Requests\GuestCategories;

use Illuminate\Http\Request;

class GuestCategoryBulkDeleteRequest
{
    private Request $request;

    public function __construct(
        Request $request
    ) {
        $this->request = $request;
    }

    public function categoryIds(): array
    {
        return $this->request->get('row');
    }
}
