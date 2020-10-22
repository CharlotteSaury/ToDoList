<?php

namespace App\Manager;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
    }

    /**
     * Handle users list recovery from database
     *
     * @return void
     */
    public function handleListAction()
    {
        return $this->userRepository->findAll();
    }

    /**
     * Handle user creation or edition in database
     *
     * @param User $user
     * @param boolean $persist
     * @param string $password
     * @return void
     */
    public function handleCreateOrUpdate(User $user, bool $persist = true, string $password = null)
    {
        if ($user->getPassword() != null) {
            $password = $this->encoder->encodePassword($user, $user->getPassword());
        }  
        $user->setPassword($password);
        if ($persist) {
            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();
    }
}
