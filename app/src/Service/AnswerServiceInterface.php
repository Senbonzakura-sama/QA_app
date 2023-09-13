<?php
/**
 * Answer service interface.
 */

namespace App\Service;

use App\Entity\Answer;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface AnswerServiceInterface.
 */
interface AnswerServiceInterface
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
     * Save entity.
     */
    public function save(Answer $answer): void;

    /**
     * Delete entity.
     */
    public function delete(Answer $answer): void;
}
