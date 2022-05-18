<?php

namespace App\Infrastructure\Doctrine\Mappings\Embeddables;

use Digbang\Utils\Doctrine\Mappings\Embeddables\EnumMapping;
use WorkshopBackoffice\Enumerables\Country;

class CountryMapping extends EnumMapping
{
    public function mapFor(): string
    {
        return Country::class;
    }
}
