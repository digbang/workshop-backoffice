<?php

namespace WorkshopBackoffice\Services;

use Doctrine\ORM\EntityNotFoundException;
use WorkshopBackoffice\Entities\GuestUser;
use WorkshopBackoffice\Payloads\GuestUserPayload;
use WorkshopBackoffice\Repositories\GuestUserRepository;
use WorkshopBackoffice\Repositories\PersistRepository;

class GuestUserService
{
    private PersistRepository $persistRepository;
    private GuestUserRepository $guestUserRepository;

    public function __construct(
        PersistRepository $persistRepository,
        GuestUserRepository $guestUserRepository
    ) {
        $this->persistRepository = $persistRepository;
        $this->guestUserRepository = $guestUserRepository;
    }

    public function create(GuestUserPayload $payload): void
    {
        $user = new GuestUser($payload);
        $this->persistRepository->save($user);
    }

    public function find(\Ramsey\Uuid\UuidInterface $id): GuestUser
    {
        $user = $this->guestUserRepository->findBy(['id' => $id->toString()]);

        if (count($user) === 0) {
            throw new EntityNotFoundException(GuestUser::class);
        }

        return array_first($user);
    }

    public function update(\Ramsey\Uuid\UuidInterface $id, GuestUserPayload $payload): void
    {
        $user = $this->find($id);
        $user->update($payload);

        $this->persistRepository->save($user);
    }

    public function delete(\Ramsey\Uuid\UuidInterface $id): void
    {
        $user = $this->find($id);

        $this->persistRepository->remove($user);
    }
}
