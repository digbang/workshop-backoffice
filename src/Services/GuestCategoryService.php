<?php

namespace WorkshopBackoffice\Services;

use Doctrine\ORM\EntityNotFoundException;
use WorkshopBackoffice\Entities\GuestCategory;
use WorkshopBackoffice\Payloads\GuestCategoryPayload;
use WorkshopBackoffice\Repositories\GuestCategoryRepository;
use WorkshopBackoffice\Repositories\PersistRepository;

class GuestCategoryService
{
    private PersistRepository $persistRepository;
    private GuestCategoryRepository $guestCategoryRepository;

    public function __construct(
        PersistRepository $persistRepository,
        GuestCategoryRepository $guestCategoryRepository
    ) {
        $this->persistRepository = $persistRepository;
        $this->guestCategoryRepository = $guestCategoryRepository;
    }

    public function create(GuestCategoryPayload $payload): void
    {
        $category = new GuestCategory($payload);
        $this->persistRepository->save($category);
    }

    public function find(\Ramsey\Uuid\UuidInterface $id): GuestCategory
    {
        $category = $this->guestCategoryRepository->findBy(['id' => $id->toString()]);

        if (count($category) === 0) {
            throw new EntityNotFoundException(GuestCategory::class);
        }

        return array_first($category);
    }

    public function update(\Ramsey\Uuid\UuidInterface $id, GuestCategoryPayload $payload): void
    {
        $category = $this->find($id);
        $category->update($payload);

        $this->persistRepository->save($category);
    }

    public function delete(\Ramsey\Uuid\UuidInterface $id): void
    {
        $category = $this->find($id);

        $this->persistRepository->remove($category);
    }

    public function bulkDelete(array $categoryIds): void
    {
        $categories = $this->guestCategoryRepository->findByIds($categoryIds);

        foreach ($categories as $category) {
            $this->persistRepository->remove($category);
        }
    }
}
