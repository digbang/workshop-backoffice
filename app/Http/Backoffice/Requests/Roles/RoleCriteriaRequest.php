<?php

namespace App\Http\Backoffice\Requests\Roles;

use App\Http\Backoffice\Requests\BackofficeCriteriaRequest;
use WorkshopBackoffice\Repositories\Criteria\Roles\RoleFilter;
use WorkshopBackoffice\Repositories\Criteria\Roles\RoleSorting;

class RoleCriteriaRequest extends BackofficeCriteriaRequest
{
    protected function getFilterClass(): string
    {
        return RoleFilter::class;
    }

    protected function getSortingClass(): string
    {
        return RoleSorting::class;
    }

    protected function getSortingDefaults(): array
    {
        return [
            RoleSorting::NAME => 'ASC',
        ];
    }
}
