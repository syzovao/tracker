<?php

namespace Oro\UserBundle\Controller;

use Oro\UserBundle\Entity\User;
use Oro\UserBundle\Form\UserType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * Show accounts list action
     *
     * @Route("/", name="oro_user_index")
     * @Template("OroUserBundle:User:index.html.twig")
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository('OroUserBundle:User');
        $users = $repository->findAll();
        return array('users' => $users);
    }

    /**
     * View user account action
     *
     * @Route("/view/{id}", name="oro_user_view", requirements={"id"="\d+"})
     * @Template("OroUserBundle:User:view.html.twig")
     *
     * @param $id
     * @return array
     */
    public function viewAction($id)
    {
        $repository = $this->getDoctrine()->getRepository('OroUserBundle:User');
        $user = $repository->findOneById($id);
        if (!$user) {
            throw $this->createNotFoundException(
                'No account found for id '.$id
            );
        }
        return array('user' => $user);
    }

    /**
     * Create user account action
     *
     * @Route("/create", name="oro_user_create")
     * @Template("OroUserBundle:User:create.html.twig")
     *
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws AccessDeniedException
     */
    public function createAction(Request $request)
    {
        $errors = array();
        if (false === $this->get('security.authorization_checker')->isGranted(array('ROLE_ADMIN', 'ROLE_USER'))) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $form = $this->createForm(new UserType($em), new User());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $form->getData();
            $user->upload();
            $em->persist($user);
            $em->flush();
            return $this->redirect($this->generateUrl('oro_user_user_index'));
        }

        return array(
            'user' => $user,
            'form' => $form->createView(),
            'errors' => $errors
        );
    }

    /**
     * @Route("/update/{id}", name="oro_user_update", requirements={"id"="\d+"})
     * @Template("OroUserBundle:User:update.html.twig")
     *
     * @param Request $request
     * @param $id
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws AccessDeniedException
     */
    public function updateAction(Request $request, $id)
    {
        $errors = array();
        if (false === $this->get('security.authorization_checker')->isGranted(array('ROLE_ADMIN', 'ROLE_USER'))) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('OroUserBundle:User')->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $form = $this->createForm(new UserType($em), $user);
        //$form->remove('password');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $form->getData();
            $user->upload();
            $em->persist($user);
            $em->flush();
            return $this->redirect(
                $this->generateUrl('oro_user_view', array('id' => $id))
            );
        }

        return array(
            'user' => $user,
            'form' => $form->createView(),
            'errors' => $errors,
        );
    }
}
