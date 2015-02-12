<?php
namespace Oro\IssueBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Oro\IssueBundle\Entity\IssueActivity;


class ActivityListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Populates identities for stored references
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!($entity instanceof IssueActivity)) {
            return;
        }

        $issue = $entity->getIssue();
        $collaborators = $issue->getCollaborators();
        if(!empty($collaborators)) {
            $mailer = $this->container->get('mailer');
            $sendFromEmail = $this->container->getParameter('oro.sender_email');
            $sendFromName = $this->container->getParameter('oro.sender_name');
            $subject = '(' . $issue->getProject() . ')' . $issue->getCode() . ': ' . $issue->getSummary();

            $sendTo = array();
            foreach ($collaborators as $user) {
                $sendTo[] = $user->getEmail();
            }
            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($sendFromEmail, $sendFromName)
                ->setTo($sendTo)
                ->setBody(
                    $this->container->get('templating')->render(
                        'OroIssueBundle:Emails:activity_' . $entity->getCode() . '.html.twig',
                        array(
                            'issue' => $issue,
                            'user' => $user,
                            'description' => $entity->getDescription(),
                            )
                    ));
            $mailer->send($message);
        }
    }
}
