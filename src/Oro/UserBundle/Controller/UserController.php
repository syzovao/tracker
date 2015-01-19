<?php

namespace Oro\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/view/{id}", name="oro_user_view", requirements={"id"="\d+"})
     * @Template()
     */
    public function viewAction(User $user)
    {
        return array();
    }

    /**
     * @Route("/create", name="oro_user_create")
     * @Template()
     */
    public function createAction()
    {
        return array();
    }

    /**
     * @Route("/update/{id}", name="oro_user_update", requirements={"id"="\d+"})
     * @Template()
     */
    public function updateAction(User $entity)
    {
        return array();
    }

}
