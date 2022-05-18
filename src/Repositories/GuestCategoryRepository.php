<?php

namespace WorkshopBackoffice\Repositories;

use WorkshopBackoffice\Repositories\Criteria\GuestCategories\GuestCategoryFilter;
use WorkshopBackoffice\Repositories\Criteria\GuestCategories\GuestCategorySorting;

interface GuestCategoryRepository extends ReadRepository
{
    public function filter(GuestCategoryFilter $filter, GuestCategorySorting $sorting, $limit = 10, $offset = 0): \Illuminate\Pagination\LengthAwarePaginator;
}
