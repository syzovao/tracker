<?php
namespace Oro\IssueBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Oro\IssueBundle\Entity\Issue;
use Oro\IssueBundle\Entity\IssueActivity;
use Oro\UserBundle\Entity\User;

class IssueListener
{
    private $token_storage;

    /**
     * Constructor
     *
     * @param TokenStorageInterface $token_storage
     */
    public function __construct(TokenStorageInterface $token_storage)
    {
        $this->token_storage = $token_storage;
    }

    /**
     * Get current User
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->token_storage->getToken()->getUser();
    }

    /**
     * Populates identities for stored references
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        /** @var Issue $entity */
        $entity = $args->getEntity();

        if ($entity instanceof Issue) {
            $entityManager = $args->getEntityManager();
            //add assignee as collaborator
            $entity->addCollaborator($entity->getAssignee());

            //track activity
            $user = $entity->getUpdatedBy();
            if (empty($user)) {
                $user = $this->getUser();
            }
            $activity = $this->createActivity($user, $entity, IssueActivity::ACTIVITY_ISSUE_CREATED);
            $entityManager->persist($activity);
            $entityManager->flush();
        }
    }

    /**
     * Create activity
     *
     * @param User $user
     * @param Issue $issue
     * @param string $code
     * @param string $description
     * @return IssueActivity
     */
    protected function createActivity($user, $issue, $code, $description = '')
    {
        $activity = new IssueActivity();
        $activity
            ->setCode($code)
            ->setIssue($issue)
            ->setUser($user)
            ->setDescription($description);
        return $activity;
    }
}
