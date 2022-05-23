<?php

namespace App\Infrastructure\Doctrine\Repositories;

use Digbang\Backoffice\Support\PaginatorFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
use WorkshopBackoffice\Entities\GuestUser;
use WorkshopBackoffice\Repositories\Criteria\GuestUsers\GuestUserFilter;
use WorkshopBackoffice\Repositories\Criteria\GuestUsers\GuestUserSorting;
use WorkshopBackoffice\Repositories\GuestUserRepository;

class DoctrineGuestUserRepository extends DoctrineReadRepository implements GuestUserRepository
{
    private PaginatorFactory $paginatorFactory;

    public function __construct(EntityManager $entityManager, PaginatorFactory $paginatorFactory)
    {
        parent::__construct($entityManager);

        $this->paginatorFactory = $paginatorFactory;
    }

    public function getEntity(): string
    {
        return GuestUser::class;
    }

    public function filter(GuestUserFilter $filter, GuestUserSorting $sorting, $limit = 10, $offset = 0): \Illuminate\Pagination\LengthAwarePaginator
    {
        $alias = 'guestUser';
        $queryBuilder = $this->createQueryBuilder($alias);

        if ($filter->has(GuestUserFilter::FIRST_NAME) && $filter->isNotEmpty(GuestUserFilter::FIRST_NAME)) {
            $term = str_replace(' ', '%', $filter->get(GuestUserFilter::FIRST_NAME));
            $queryBuilder->andWhere($queryBuilder->expr()->like("LOWER(UNACCENT($alias.name.firstName))", 'LOWER(UNACCENT(:firstName))'));
            $queryBuilder->setParameter(GuestUserFilter::FIRST_NAME, "%{$term}%");
        }

        if ($filter->has(GuestUserFilter::LAST_NAME) && $filter->isNotEmpty(GuestUserFilter::LAST_NAME)) {
            $term = str_replace(' ', '%', $filter->get(GuestUserFilter::LAST_NAME));
            $queryBuilder->andWhere($queryBuilder->expr()->like("LOWER(UNACCENT($alias.name.lastName))", 'LOWER(UNACCENT(:lastName))'));
            $queryBuilder->setParameter(GuestUserFilter::LAST_NAME, "%{$term}%");
        }

        if ($filter->has(GuestUserFilter::COUNTRY) && $filter->isNotEmpty(GuestUserFilter::COUNTRY)) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq("LOWER($alias.country.value)", 'LOWER(:country)'));
            $queryBuilder->setParameter(GuestUserFilter::COUNTRY, $filter->get(GuestUserFilter::COUNTRY));
        }

        foreach ($sorting->getRaw() as $sortBy => $sortSense) {
            $queryBuilder->addOrderBy("$alias.$sortBy", $sortSense);
        }

        $queryBuilder
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $this->paginatorFactory->fromDoctrinePaginator(
            new Paginator($queryBuilder)
        );
    }
}
