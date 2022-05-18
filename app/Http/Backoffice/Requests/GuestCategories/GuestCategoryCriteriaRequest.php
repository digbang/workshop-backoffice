<?php

namespace App\Http\Backoffice\Requests\GuestCategories;

use App\Http\Backoffice\Requests\BackofficeCriteriaRequest;
use WorkshopBackoffice\Repositories\Criteria\GuestCategories\GuestCategoryFilter;
use WorkshopBackoffice\Repositories\Criteria\GuestCategories\GuestCategorySorting;

class GuestCategoryCriteriaRequest extends BackofficeCriteriaRequest
{
    protected function getFilterClass(): string
    {
        return GuestCategoryFilter::class;
    }

    protected function getSortingClass(): string
    {
        return GuestCategorySorting::class;
    }
}
