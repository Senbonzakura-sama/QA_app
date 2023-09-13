<?php
/**
 * Registration service.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class RegistrationService.
 */
class RegistrationService
{
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * User repository.
     */
    private UserRepository $userRepository;

    /**
     * RegistrationService constructor.
     *
     * @param UserRepository              $userRepository User repository
     * @param UserPasswordHasherInterface $passwordHasher Password Hasher
     */
    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Save user.
     *
     * @param User $user User entity
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }

    /**
     * Register a new user.
     *
     * @param array $data User data (e.g., email, nickname, password)
     */
    public function register(array $data): void
    {
        $user = new User();
        $user->setEmail($data['email']);
        $user->setNickname($data['nickname']);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $data['password'])
        );
        $user->setRoles(['ROLE_USER']);

        $this->userRepository->save($user, true);
    }
}
