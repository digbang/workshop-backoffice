<?php

namespace App\Http\Backoffice\Requests\GuestUsers;

use App\Http\Backoffice\Requests\BackofficeCriteriaRequest;
use WorkshopBackoffice\Repositories\Criteria\GuestUsers\GuestUserFilter;
use WorkshopBackoffice\Repositories\Criteria\GuestUsers\GuestUserSorting;

class GuestUserCriteriaRequest extends BackofficeCriteriaRequest
{
    protected function getFilterClass(): string
    {
        return GuestUserFilter::class;
    }

    protected function getSortingClass(): string
    {
        return GuestUserSorting::class;
    }
}
