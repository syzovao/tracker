<?php
namespace Oro\IssueBundle\Entity;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class IssueActivityRepository extends EntityRepository
{
    /**
     * Get issues where user is assigned
     *
     * @param $projectId
     * @param $userId
     * @param $issueId
     * @return array
     * @throws EntityNotFoundException
     */
    public function findByParams($projectId = null, $issueId = null, $userId = null)
    {
        $q = $this->createQueryBuilder('a')
            ->select('a')
            ->addOrderBy('a.createdAt', 'DESC');

        if(!is_null($projectId)){
            $q
                ->join('a.issue', 'issue')
                ->where('issue.project = :projectId')
                ->setParameter('projectId', $projectId);
        }

        if(!is_null($issueId)){
            $q->where('a.issue = :issueId')
                ->setParameter('issueId', $issueId);
        }

        if(!is_null($userId)){
            $q->where('a.user = :userId')
                ->setParameter('userId', $userId);
        }


        try {
            // The Query::getResult() method throws an exception
            // if there is no record matching the criteria.
            $activities = $q->getQuery()->getResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find an active admin IssueBundle:IssueActivity object identified by "%s".',
                $issueId
            );
            throw new EntityNotFoundException($message, 0, $e);
        }

        return $activities;
    }
}
