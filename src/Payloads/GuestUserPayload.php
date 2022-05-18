<?php

namespace WorkshopBackoffice\Payloads;

use Digbang\Security\Users\ValueObjects\Name;
use WorkshopBackoffice\Enumerables\Country;

interface GuestUserPayload
{
    public function name(): Name;

    public function country(): Country;
}
