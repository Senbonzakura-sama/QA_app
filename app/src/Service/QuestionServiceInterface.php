<?php
/**
 * Question service interface.
 */

namespace App\Service;

use App\Entity\Question;
use App\Entity\User;
use App\Entity\Category;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface QuestionServiceInterface.
 */
interface QuestionServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Get paginated list by Author.
     *
     * @param int  $page   Page number
     * @param User $author Author
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByAuthor(int $page, User $author): PaginationInterface;

    /**
     * Get paginated list by Category.
     *
     * @param int      $page     Page number
     * @param Category $category Category
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByCategory(int $page, Category $category): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Question $question Question entity
     */
    public function save(Question $question): void;

    /**
     * Delete entity.
     *
     * @param Question $question Question entity
     */
    public function delete(Question $question): void;
}
