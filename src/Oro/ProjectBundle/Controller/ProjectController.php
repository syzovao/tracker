<?php

namespace Oro\ProjectBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oro\ProjectBundle\Entity\Project;
use Oro\ProjectBundle\Form\ProjectType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Project controller.
 *
 * @Route("/project")
 */
class ProjectController extends Controller
{

    /**
     * Lists all Project entities.
     *
     * @Route("/", name="oro_project")
     * @Template("OroProjectBundle:Project:index.html.twig")
     */
    public function indexAction()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $username = $this->getUser()->getUsername();
        $entity = new Project();
        if (false === $this->get('security.authorization_checker')->isGranted('VIEW_LIST', $entity, $username)) {
            $entities = $em->getRepository('OroProjectBundle:Project')->findByProjectMember($this->getUser()->getId());
        } else {
            $entities = $em->getRepository('OroProjectBundle:Project')->findAll();
        }
        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Project entity.
     *
     * @Route("/create", name="oro_project_create")
     * @Template("OroProjectBundle:Project:create.html.twig")
     *
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws AccessDeniedException
     */
    public function createAction(Request $request)
    {
        $entity = new Project();
        if (false === $this->get('security.authorization_checker')->isGranted('MODIFY', $entity)) {
            throw new AccessDeniedException('project.validators.permissions_denied_modify');
        }

        $em = $this->getDoctrine()->getManager();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity = $form->getData();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('oro_project_view', array('id' => $entity->getId())));
        }

        return array(
            'user' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/{id}", name="oro_project_view", requirements={"id"="\d+"})
     * @ParamConverter("entity", class="OroProjectBundle:Project")
     * @Template()
     *
     * @param Project $entity
     * @return array
     */
    public function viewAction(Project $entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('project.messages.entity_not_found'));
        }

        if (false === $this->get('security.authorization_checker')->isGranted('VIEW', $entity)) {
            throw new AccessDeniedException('project.validators.permissions_denied_view');
        }

        $deleteForm = $this->createDeleteForm($entity);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Project entity.
     *
     * @Route("/update/{id}", name="oro_project_update", requirements={"id"="\d+"})
     * @ParamConverter("entity", class="OroProjectBundle:Project")
     * @Template("OroProjectBundle:Project:update.html.twig")
     *
     * @param Project $entity
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws AccessDeniedException
     */
    public function updateAction(Project $entity, Request $request)
    {
        $errors = array();
        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('project.messages.entity_not_found'));
        }
        if (false === $this->get('security.authorization_checker')->isGranted('MODIFY', $entity)) {
            throw new AccessDeniedException('project.validators.permissions_denied_modify');
        }

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirect($this->generateUrl('oro_project_view', array('id' => $entity->getId())));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errors' => $errors
        );
    }

    /**
     * Deletes a Project entity.
     *
     * @Route("/delete/{id}", name="oro_project_delete", requirements={"id"="\d+"})
     * @ParamConverter("entity", class="OroProjectBundle:Project")
     * @Method("DELETE")
     *
     * @param Project $entity
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Project $entity, Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('MODIFY', $entity)) {
            throw new AccessDeniedException('project.validators.permissions_denied_modify');
        }

        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OroProjectBundle:Project')->find($entity->getId());
            if (!$entity) {
                throw $this->createNotFoundException(
                    $this->get('translator')->trans('project.messages.entity_not_found')
                );
            }
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('oro_project'));
    }

    /**
     * Creates a form to create a Project entity.
     *
     * @param Project $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Project $entity)
    {
        $form = $this->createForm(new ProjectType(), $entity);
        $form->add('submit', 'submit', array('label' => 'Create'));
        return $form;
    }

    /**
     * Creates a form to edit a Project entity.
     *
     * @param Project $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Project $entity)
    {
        $form = $this->createForm(new ProjectType(), $entity);
        $form->add('submit', 'submit', array('label' => 'Update'));
        return $form;
    }

    /**
     * Creates a form to delete a Project entity by id.
     *
     * @param Project $entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Project $entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('oro_project_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}
