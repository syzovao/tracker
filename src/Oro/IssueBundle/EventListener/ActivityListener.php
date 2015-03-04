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

        $user = $entity->getUser();
        $issue = $entity->getIssue();
        $collaborators = $issue->getCollaborators();
        if(!empty($collaborators)) {

            $sendFromEmail = $this->container->getParameter('oro.sender_email');
            $sendFromName = $this->container->getParameter('oro.sender_name');
            $subject = '(' . $issue->getProject() . ')' . $issue->getCode() . ': ' . $issue->getSummary();

            $sendTo = array();
            foreach ($collaborators as $collaborator) {
                $sendTo[] = $collaborator->getEmail();
            }

            $body =  $this->container->get('templating')->render(
                'OroIssueBundle:Emails:activity_' . $entity->getCode() . '.html.twig',
                array(
                    'issue' => $issue,
                    'user' => $user,
                    'description' => $entity->getDescription(),
                )
            );
            $this->sendEmailAction($sendTo, $subject, $body, $sendFromEmail, $sendFromName);
        }
    }

    /**
     * Send email
     *
     * @param string $sendTo
     * @param string|null $sendToName
     * @param string $sendFromEmail
     * @param string|null $sendFromName
     * @param string $subject
     * @param string $body
     */
    public function sendEmailAction($sendTo, $subject, $body, $sendFromEmail, $sendFromName = null, $sendToName = null)
    {
        $mailer = $this->container->get('mailer');
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($sendFromEmail, $sendFromName)
            ->setTo($sendTo, $sendToName)
            ->setBody($body);
        $mailer->send($message);
    }
}
