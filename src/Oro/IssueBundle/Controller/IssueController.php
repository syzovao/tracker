<?php

namespace Oro\IssueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IssueController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('OroIssueBundle:Default:index.html.twig', array('name' => $name));
    }
}
