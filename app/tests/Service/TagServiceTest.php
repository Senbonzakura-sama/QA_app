<?php
/**
 * Tag service test
 */
namespace App\Tests\Service;

use App\Entity\Tag;
use App\Service\TagService;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class TagServiceTest
 */
class TagServiceTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;

    private ?TagService $tagService;

    /**
     * Set up test
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->tagService = $container->get(TagService::class);
    }

    /**
     * Save test
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testSave(): void
    {
        //given
        $expectedTag = new Tag();
        $expectedTag->setCreatedAt(new DateTimeImmutable());
        $expectedTag->setUpdatedAt(new DateTimeImmutable());
        $expectedTag->setTitle('Tag Test');

        //when
        $this->tagService->save($expectedTag);

        //then
        $expectedTagId = $expectedTag->getId();
        $resultTag = $this->entityManager->createQueryBuilder()
            ->select('tag')
            ->from(Tag::class, 'tag')
            ->where('tag.id = :id')
            ->setParameter(':id', $expectedTagId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($expectedTag, $resultTag);
    }

    /**
     * Delete test
     *
     * @throws NonUniqueResultException
     */
    public function testDelete(): void
    {
        //given
        $tagToDelete = new Tag();
        $tagToDelete->setCreatedAt(new DateTimeImmutable());
        $tagToDelete->setUpdatedAt(new DateTimeImmutable());
        $tagToDelete->setTitle('Tag Test');

        $this->entityManager->persist($tagToDelete);
        $this->entityManager->flush();
        $deletedTagId = $tagToDelete->getId();

        //when
        $this->tagService->delete($tagToDelete);

        //then
        $resultTag = $this->entityManager->createQueryBuilder()
            ->select('tag')
            ->from(Tag::class, 'tag')
            ->where('tag.id = :id')
            ->setParameter(':id', $deletedTagId, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($resultTag);
    }

    /**
     * Paginated list test
     *
     */
    public function testPaginatedList(): void
    {
        //given
        $page = 1;
        $dataSetSize = 3;
        $expectedResultSize = 3;

        $counter = 0;

        while ($counter < $dataSetSize) {
            $tag = new Tag();
            $tag->setCreatedAt(new DateTimeImmutable());
            $tag->setUpdatedAt(new DateTimeImmutable());
            $tag->setTitle('Tag Test #'.$counter);
            $this->tagService->save($tag);

            ++$counter;
        }

        //when
        $result = $this->tagService->getPaginatedList($page);

        //then
        $this->assertEquals($expectedResultSize, $result->count());
    }

    /**
     * Find one by id test
     *
     */
    public function testFindOneById(): void
    {
        //given
        $expectedTag = new Tag();
        $expectedTag->setCreatedAt(new DateTimeImmutable());
        $expectedTag->setUpdatedAt(new DateTimeImmutable());
        $expectedTag->setTitle('Tag Test');
        $this->entityManager->persist($expectedTag);
        $this->entityManager->flush();
        $expectedTagId = $expectedTag->getId();

        //when
        $resultTag = $this->tagService->findOneById($expectedTagId);

        //then
        $this->assertEquals($expectedTag, $resultTag);

    }

    /**
     * Find one by title test
     *
     */
    public function testFindOneByTitle(): void
    {
        //given
        $expectedTag = new Tag();
        $expectedTag->setTitle('TagTest'); // Ustaw tylko pole "title" - to jest kluczowe w tym teście
        $this->entityManager->persist($expectedTag);
        $this->entityManager->flush();

        //when
        $resultTag = $this->tagService->findOneByTitle('TagTest');

        //then
        $this->assertEquals($expectedTag->getTitle(), $resultTag->getTitle());
    }
}
