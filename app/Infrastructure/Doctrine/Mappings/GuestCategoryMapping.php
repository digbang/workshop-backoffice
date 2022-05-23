<?php

namespace App\Infrastructure\Doctrine\Mappings;

use Digbang\DoctrineExtensions\Types\UuidType;
use LaravelDoctrine\Fluent\EntityMapping;
use LaravelDoctrine\Fluent\Fluent;
use WorkshopBackoffice\Entities\GuestCategory;
use WorkshopBackoffice\Entities\GuestUser;

class GuestCategoryMapping extends EntityMapping
{
    public function mapFor(): string
    {
        return GuestCategory::class;
    }

    public function map(Fluent $builder): void
    {
        $builder->field(UuidType::UUID, 'id')->primary();
        $builder->string('name');
        $builder->timestamps('createdAt', 'updatedAt', 'chronosDateTime');
        $builder->manyToMany(GuestUser::class, 'users')
            ->mappedBy('categories');
    }
}
