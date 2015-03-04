<?php

namespace Oro\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     * @Template("OroUserBundle:Default:index.html.twig")
     */
    public function dashboardAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $issues = $em->getRepository('OroIssueBundle:Issue')->findByUserCollaborator($user->getId());
        $activities = $em->getRepository('OroIssueBundle:IssueActivity')->findByProjectMember($user->getId());
        return array(
            'issues' => $issues,
            'activities' => $activities
        );
    }
}
