<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\RegistrationService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationServiceTest extends TestCase
{
    private RegistrationService $registrationService;

    public function setUp(): void
    {
        $userRepository = $this->createMock(UserRepository::class);
        $passwordHashes = $this->createMock(UserPasswordHasherInterface::class);

        $this->registrationService = new RegistrationService($userRepository, $passwordHashes);
    }



    public function testSaveUser()
    {
        // Przygotuj testowe dane użytkownika
        $userData = [
            'email' => 'test@example.com',
            'nickname' => 'testuser',
            'password' => 'hashed_password', // Załóżmy, że hasło zostało już zahaszowane wcześniej
        ];

        // Utwórz obiekt użytkownika
        $user = new User();
        $user->setEmail($userData['email']);
        $user->setNickname($userData['nickname']);
        $user->setPassword($userData['password']);
        $user->setRoles(['ROLE_USER']);

        // Utwórz atrapę repozytorium użytkowników
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->once())
            ->method('save')
            ->with($this->equalTo($user)); // Użyj $this->equalTo

        // Utwórz atrapę haszera hasła
        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);

        // Utwórz usługę rejestracji
        $registrationService = new RegistrationService($userRepository, $passwordHasher);

        // Wywołaj metodę save
        $registrationService->save($user);
    }
}
