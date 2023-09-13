<?php
// tests/Service/UserServiceTest.php

namespace App\Tests\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private $userRepository;
    private $paginator;
    private $userService;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);
        $this->userService = new UserService($this->userRepository, $this->paginator);
    }

    public function testGetPaginatedList()
    {
        $page = 1;
        $queryBuilder = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
        $expectedPagination = $this->createMock(PaginationInterface::class);

        // Mocking userRepository->queryAll() method
        $this->userRepository->expects($this->once())
            ->method('queryAll')
            ->willReturn($queryBuilder);

        // Mocking paginator->paginate() method
        $this->paginator->expects($this->once())
            ->method('paginate')
            ->with(
                $queryBuilder,
                $page,
                UserRepository::PAGINATOR_ITEMS_PER_PAGE
            )
            ->willReturn($expectedPagination);

        $pagination = $this->userService->getPaginatedList($page);

        $this->assertSame($expectedPagination, $pagination);
    }

    public function testSave()
    {
        $user = new User(); // Create a User object as needed

        // Mocking userRepository->save() method
        $this->userRepository->expects($this->once())
            ->method('save')
            ->with($user, true);

        $this->userService->save($user);
    }

    public function testDelete()
    {
        $user = new User(); // Create a User object as needed

        // Mocking userRepository->remove() method
        $this->userRepository->expects($this->once())
            ->method('remove')
            ->with($user, true);

        $this->userService->delete($user);
    }

    public function testFindOneByEmail()
    {
        $email = 'test@example.com'; // Replace with your test email
        $user = new User(); // Create a User object as needed

        // Mocking userRepository->findOneBy() method
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => $email])
            ->willReturn($user);

        $result = $this->userRepository->findOneBy(['email' => $email]);

        $this->assertSame($user, $result);
    }

    public function testFindOneByEmailNotFound()
    {
        $email = 'nonexistent@example.com';

        // Mockowanie UserRepository bez wywołań metod
        $this->userRepository->expects($this->never()) // Oczekujemy, że UserRepository nie zostanie wywołane
        ->method('queryAll');
        $this->userRepository->expects($this->never())
            ->method('findOneBy');

        $result = $this->userService->findOneByEmail($email);

        $this->assertNull($result);
    }

    public function testDeleteNonExistentUser()
    {
        $user = new User(); // Create a User object as needed

        // Mocking userRepository->remove() method to throw an exception
        $this->userRepository->expects($this->once())
            ->method('remove')
            ->with($user, true)
            ->willThrowException(new \Exception('User not found'));

        $this->expectException(\Exception::class);
        $this->userService->delete($user);
    }


}
