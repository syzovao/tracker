<?php

namespace Oro\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('OroUserBundle:Default:index.html.twig');
    }
}
