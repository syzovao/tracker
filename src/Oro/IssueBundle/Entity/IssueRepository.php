<?php
namespace Oro\IssueBundle\Entity;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Oro\IssueBundle\Entity\IssueStatus;

class IssueRepository extends EntityRepository
{
    /**
     * Get issues where user is assigned
     *
     * @param $userId
     * @return array
     * @throws EntityNotFoundException
     */
    public function findByUser($userId)
    {
        $q = $this
            ->createQueryBuilder('u')
            ->where('u.assignee = :assignee')
            ->setParameter('assignee', $userId)
            ->getQuery();

        try {
            // The Query::getResult() method throws an exception
            // if there is no record matching the criteria.
            $issues = $q->getResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find an active admin IssueBundle:Issue object identified by "%s".',
                $userId
            );
            throw new EntityNotFoundException($message, 0, $e);
        }

        return $issues;
    }

    /**
     * Get issues where user is assigned
     *
     * @param $userId
     * @return array
     * @throws EntityNotFoundException
     */
    public function findByUserCollaborator($userId)
    {
        $q = $this
            ->createQueryBuilder('u')
            ->join('u.issueStatus', 's')
            ->where('u.assignee = :assignee OR u.reporter = :reporter')
            ->andWhere("s.code IN ('open', 'reopened')")
            ->setParameter('assignee', $userId)
            ->setParameter('reporter', $userId)
            ->getQuery();

        try {
            // The Query::getResult() method throws an exception
            // if there is no record matching the criteria.
            $issues = $q->getResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find an active admin IssueBundle:Issue object identified by "%s".',
                $userId
            );
            throw new EntityNotFoundException($message, 0, $e);
        }

        return $issues;
    }
}
