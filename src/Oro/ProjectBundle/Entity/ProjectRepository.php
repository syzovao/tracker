<?php
namespace Oro\ProjectBundle\Entity;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;


class ProjectRepository extends EntityRepository
{
    /**
     * Get projects where user is assigned
     *
     * @param $userId
     * @return array
     * @throws EntityNotFoundException
     */
    public function findByProjectMember($userId)
    {
        $q = $this->queryProjectMember($userId);
        try {
            // The Query::getResult() method throws an exception
            // if there is no record matching the criteria.
            $projects = $q->getQuery()->getResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find a ProjectBundle:Project object identified by "%s".',
                $userId
            );
            throw new EntityNotFoundException($message, 0, $e);
        }

        return $projects;
    }

    /**
     * Get Project Member query
     *
     * @param $userId
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function queryProjectMember($userId)
    {
        $q = $this->createQueryBuilder('p')
            ->select('p')
            ->join('p.users', 'u')
            ->where('u.id = :user_id')
            ->setParameter('user_id', $userId);
        return $q;
    }
}
