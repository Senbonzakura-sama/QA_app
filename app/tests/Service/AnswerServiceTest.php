<?php

namespace App\Tests\Service;

use App\Entity\Answer;
use App\Repository\AnswerRepository;
use App\Service\AnswerService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Knp\Component\Pager\PaginatorInterface;

class AnswerServiceTest extends TestCase
{
    private $answerRepository;
    private $paginator;
    private $entityManager;
    private $parameterBag;

    protected function setUp(): void
    {
        // Tworzymy mock repozytorium AnswerRepository
        $this->answerRepository = $this->createMock(AnswerRepository::class);

        // Tworzymy mock PaginatorInterface
        $this->paginator = $this->createMock(PaginatorInterface::class);

        // Tworzymy mock EntityManagerInterface
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        // Tworzymy mock ParameterBagInterface (jeśli jest potrzebny)
        $this->parameterBag = $this->createMock(ParameterBagInterface::class);
    }

    public function testGetPaginatedList()
    {
        // Tworzenie mocka PaginatorInterface
        $paginatorMock = $this->createMock(PaginatorInterface::class);

        // Tworzenie mocka PaginationInterface
        $paginationMock = $this->createMock(PaginationInterface::class);

        // Tworzenie mocka AnswerRepository
        $answerRepositoryMock = $this->createMock(AnswerRepository::class);

        // Ustalanie oczekiwanej metody paginate w mocku PaginatorInterface
        $paginatorMock->expects($this->once())
            ->method('paginate')
            ->willReturn($paginationMock); // Zwracamy mock PaginationInterface

        // Tworzenie serwisu AnswerService z mockami PaginatorInterface i AnswerRepository
        $answerService = new AnswerService($answerRepositoryMock, $paginatorMock);

        // Wywołanie metody getPaginatedList na serwisie
        $result = $answerService->getPaginatedList(1);

        // Sprawdzenie, czy wynik to oczekiwany mock PaginationInterface
        $this->assertSame($paginationMock, $result);
    }

    public function testSaveAnswer()
    {
        // Tworzymy instancję AnswerService, przekazując nasze mocki
        $answerService = new AnswerService($this->answerRepository, $this->paginator);

        // Tworzymy przykładową odpowiedź
        $answer = new Answer();

        // Ustawiamy oczekiwane zachowanie mocka repozytorium
        $this->answerRepository->expects($this->once())
            ->method('save')
            ->with($answer);

        // Wywołujemy testowaną metodę
        $answerService->save($answer);
    }

    public function testDeleteAnswer()
    {
        // Tworzymy instancję AnswerService, przekazując nasze mocki
        $answerService = new AnswerService($this->answerRepository, $this->paginator);

        // Tworzymy przykładową odpowiedź
        $answer = new Answer();

        // Ustawiamy oczekiwane zachowanie mocka repozytorium
        $this->answerRepository->expects($this->once())
            ->method('delete')
            ->with($answer);

        // Wywołujemy testowaną metodę
        $answerService->delete($answer);
    }

    public function testFindByQuestion()
    {
        // Tworzymy instancję AnswerService, przekazując nasze mocki
        $answerService = new AnswerService($this->answerRepository, $this->paginator);

        // Tworzymy przykładowe kryteria wyszukiwania
        $criteria = ['question' => 'example'];

        // Ustawiamy oczekiwane zachowanie mocka repozytorium
        $this->answerRepository->expects($this->once())
            ->method('findBy')
            ->with($criteria)
            ->willReturn([]); // Tutaj możesz zwrócić odpowiednie dane, które chcesz użyć w teście

        // Wywołujemy testowaną metodę
        $result = $answerService->findBy($criteria);

        // Sprawdzamy, czy wynik jest taki, jakiego się spodziewamy
        $this->assertEquals([], $result);
    }
}
