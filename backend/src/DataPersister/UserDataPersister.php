<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;

class UserDataPersister implements ContextAwareDataPersisterInterface
{
    /** @var DataPersisterInterface */
    private $decorated;

    /**
     * UserDataPersister constructor.
     */
    public function __construct(DataPersisterInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * @param mixed $data
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User $data
     *
     * @return object|void
     */
    public function persist($data, array $context = [])
    {
        // if create
        if ('post' !== $context['collection_operation_name']) {
            $data->setResetPasswordToken('todo'); // todo token
            $data->setResetPasswordAt(new \DateTime());
            $data->addRole('ROLE_USER');

            $r = $this->decorated->persist($data);

            // todo email

            return $r;
        }

        return $data;
    }

    /**
     * @param User $data
     */
    public function remove($data, array $context = []): void
    {
        // on remove pas
    }
}
