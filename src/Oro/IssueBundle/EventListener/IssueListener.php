<?php
namespace Oro\IssueBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Oro\IssueBundle\Entity\Issue;
use Oro\IssueBundle\Entity\IssueActivity;
use Oro\UserBundle\Entity\User;

class IssueListener
{
    /**
     * @var array
     */
    private $activities;

    /**
     * @var TokenStorageInterface
     */
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
     * The preUpdate event occurs before the database update operations to
     * entity data.
     *
     * @param PreUpdateEventArgs $eventArgs
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        /** @var Issue $entity */
        $entity = $eventArgs->getObject();
        if ($entity instanceof Issue) {
            if ($eventArgs->hasChangedField('issueStatus')) {
                $old = $eventArgs->getOldValue('issueStatus')->getName();
                $new = $eventArgs->getNewValue('issueStatus')->getName();
                $description = sprintf("Issue status changed from %s to %s", $old, $new);
                $activity = $this->createActivity(
                    $this->getUser(),
                    $entity,
                    IssueActivity::ACTIVITY_ISSUE_STATUS,
                    $description);
                $this->activities[] = $activity;
            }
        }
    }

    /**
     * preFlush event.
     *
     * @param OnFlushEventArgs $event
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        $this->activities = array();
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $event->getEntityManager();
        /* @var $uow \Doctrine\ORM\UnitOfWork */
        $uow = $em->getUnitOfWork();

        /**
         * Gets the currently scheduled entity insertions in this UnitOfWork
         * Gets the currently scheduled entity updates in this UnitOfWork.
         */
        $entities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates()
        );

        foreach ($entities as $entity) {
            if (!($entity instanceof Issue)) {
                continue;
            }
            $entity->addCollaborator($entity->getAssignee());
            $em->persist($entity);
            $md = $em->getClassMetadata(get_class($entity));
            $uow->recomputeSingleEntityChangeSet($md, $entity);
        }
    }

    /**
     * postFlush event
     *
     * @param PostFlushEventArgs $event
     */
    public function postFlush(PostFlushEventArgs $event)
    {
        if (!empty($this->activities)) {
            /* @var $em \Doctrine\ORM\EntityManager */
            $em = $event->getEntityManager();
            foreach ($this->activities as $activity) {
                /* @var $activity \Oro\IssueBundle\Entity\IssueActivity */
                $em->persist($activity);
            }
            $this->activities = array();
            $em->flush();
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
