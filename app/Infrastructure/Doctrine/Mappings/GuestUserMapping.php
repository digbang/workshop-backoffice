<?php

namespace App\Infrastructure\Doctrine\Mappings;

use Digbang\DoctrineExtensions\Types\UuidType;
use Digbang\Security\Users\ValueObjects\Name;
use LaravelDoctrine\Fluent\EntityMapping;
use LaravelDoctrine\Fluent\Fluent;
use WorkshopBackoffice\Entities\GuestCategory;
use WorkshopBackoffice\Entities\GuestUser;
use WorkshopBackoffice\Enumerables\Country;

class GuestUserMapping extends EntityMapping
{
    public function mapFor(): string
    {
        return GuestUser::class;
    }

    public function map(Fluent $builder): void
    {
        $builder->field(UuidType::UUID, 'id')->primary();
        $builder->embed(Name::class)->noPrefix();
        $builder->embed(Country::class);
        $builder->string('description');
        $builder->string('address');
        $builder->boolean('isActivated');
        $builder->boolean('wishToBeContacted');
        $builder->boolean('canBeEdited')->default('true');
        $builder->manyToMany(GuestCategory::class, 'categories')
            ->inversedBy('users');
        $builder->integer('phoneNumber');
        $builder->string('comments');
        $builder->chronosDateTime('birthdate');
        $builder->chronosDateTime('admissionDate');
        $builder->timestamps('createdAt', 'updatedAt', 'chronosDateTime');
    }
}
