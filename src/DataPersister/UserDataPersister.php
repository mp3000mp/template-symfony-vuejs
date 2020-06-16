<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class UserDataPersister implements ContextAwareDataPersisterInterface
{

    /** @var DataPersisterInterface  */
    private $decorated;

    /**
     * UserDataPersister constructor.
     *
     * @param DataPersisterInterface $decorated
     */
    public function __construct(DataPersisterInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User $data
     * @param array $context
     *
     * @return object|void
     */
    public function persist($data, array $context = [])
    {
        // if create
        if ($context['collection_operation_name'] !== 'post') {
            $data->setResetPasswordToken('todo'); // todo token
            $data->setResetPasswordAt(new \DateTime());
            $data->addRole('ROLE_USER');

            $r = $this->decorated->persist($data);

            // todo email

            return $r;
        }

        return $data;
    }

    public function remove($data, array $context = [])
    {
        // on remove pas
    }

}
