<?php

namespace App\Infrastructure\Doctrine\Repositories;

use Digbang\Backoffice\Support\PaginatorFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
use WorkshopBackoffice\Entities\GuestCategory;
use WorkshopBackoffice\Repositories\Criteria\GuestCategories\GuestCategoryFilter;
use WorkshopBackoffice\Repositories\Criteria\GuestCategories\GuestCategorySorting;
use WorkshopBackoffice\Repositories\GuestCategoryRepository;

class DoctrineGuestCategoryRepository extends DoctrineReadRepository implements GuestCategoryRepository
{
    private PaginatorFactory $paginatorFactory;

    public function __construct(EntityManager $entityManager, PaginatorFactory $paginatorFactory)
    {
        parent::__construct($entityManager);

        $this->paginatorFactory = $paginatorFactory;
    }

    public function getEntity(): string
    {
        return GuestCategory::class;
    }

    public function filter(GuestCategoryFilter $filter, GuestCategorySorting $sorting, $limit = 10, $offset = 0): \Illuminate\Pagination\LengthAwarePaginator
    {
        $alias = 'guestCategory';
        $queryBuilder = $this->createQueryBuilder($alias);

        if ($filter->has(GuestCategoryFilter::NAME) && $filter->isNotEmpty(GuestCategoryFilter::NAME)) {
            $term = str_replace(' ', '%', $filter->get(GuestCategoryFilter::NAME));
            $queryBuilder->andWhere($queryBuilder->expr()->like("LOWER(UNACCENT($alias.name))", 'LOWER(UNACCENT(:name))'));
            $queryBuilder->setParameter(GuestCategoryFilter::NAME, "%{$term}%");
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
