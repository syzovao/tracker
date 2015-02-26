<?php

namespace Oro\UserBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oro\UserBundle\Entity\User;
use Oro\UserBundle\Form\UserType;

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
            $message = $this->get('translator')->trans('No account found for id %id%', array('%id%' => $id));
            throw $this->createNotFoundException($message);
        }

        $em = $this->getDoctrine()->getManager();
        $issues = $em->getRepository('OroIssueBundle:Issue')->findByUser($user->getId());

        return array(
            'user' => $user,
            'issues' => $issues,
        );
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
        if (false === $this->get('security.authorization_checker')->isGranted(array('ROLE_ADMIN'))) {
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
            return $this->redirect($this->generateUrl('oro_user_index'));
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
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('OroUserBundle:User')->find($id);

        if (false === $this->get('security.authorization_checker')->isGranted('MODIFY', $user)) {
            throw new AccessDeniedException('user.validators.permissions_denied_edit');
        }

        if (!$user) {
            throw $this->createNotFoundException($this->get('translator')->trans('user.messages.entity_not_found'));
        }

        $form = $this->createForm(new UserType($em), $user);

        //$form->remove('password');
        if (false === $this->get('security.authorization_checker')->isGranted('EDIT_ROLE', $user)) {
            $field = $form->get('role')->getConfig();
            $options = $field->getOptions();
            $type = $field->getType()->getName();
            $options['read_only'] = true;
            $form->add('role', $type, $options);
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $form->getData();
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
            $user->setPassword($encoder->encodePassword($user->getPassword(), $user->getSalt()));
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
