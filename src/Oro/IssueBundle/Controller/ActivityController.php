<?php

namespace Oro\IssueBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Oro\IssueBundle\Entity\IssueActivity;

/**
 * IssueActivity controller.
 *
 * @Route("/activity")
 */
class ActivityController extends Controller
{

    /**
     * Lists all IssueActivity entities.
     *
     * @param null $projectId
     * @param null $issueId
     * @param null $userId
     * @return array
     *
     * @Template("OroIssueBundle:Activity:index.html.twig")
     */
    public function indexAction($projectId = null, $issueId = null, $userId = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OroIssueBundle:IssueActivity')->findByParams($projectId, $issueId, $userId);
        return array(
            'entities' => $entities,
        );
    }

}
