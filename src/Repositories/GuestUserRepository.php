<?php

namespace WorkshopBackoffice\Repositories;

use WorkshopBackoffice\Repositories\Criteria\GuestUsers\GuestUserFilter;
use WorkshopBackoffice\Repositories\Criteria\GuestUsers\GuestUserSorting;

interface GuestUserRepository extends ReadRepository
{
    public function filter(GuestUserFilter $filter, GuestUserSorting $sorting, $limit = 10, $offset = 0): \Illuminate\Pagination\LengthAwarePaginator;
}
