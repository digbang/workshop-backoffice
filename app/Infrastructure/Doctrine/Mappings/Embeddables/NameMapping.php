<?php

namespace App\Infrastructure\Doctrine\Mappings\Embeddables;

use LaravelDoctrine\Fluent\EmbeddableMapping;
use LaravelDoctrine\Fluent\Fluent;
use WorkshopBackoffice\Immutables\Name;

class NameMapping extends EmbeddableMapping
{
    public function mapFor(): string
    {
        return Name::class;
    }

    public function map(Fluent $builder): void
    {
        $builder->text('firstName');
        $builder->text('lastName');
    }
}
