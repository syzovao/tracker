<?php

namespace Oro\IssueBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
#use Symfony\Component\Validator\Constraints\DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Oro\IssueBundle\Entity\Issue;
use Oro\IssueBundle\Entity\IssueType as IssueTypeEntity;
use Oro\IssueBundle\Form\IssueType;

/**
 * Issue controller.
 *
 * @Route("/issue")
 */
class IssueController extends Controller
{

    /**
     * Lists all Issue entities.
     *
     * @Route("/", name="oro_issue")
     * @Method("GET")
     * @Template("OroIssueBundle:Issue:index.html.twig")
     */
    public function indexAction()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OroIssueBundle:Issue')->findAll();
        $userEntities = $em->getRepository('OroIssueBundle:Issue')->findByUser($this->getUser()->getId());
        return array(
            'entities' => $entities,
            'user_entities' => $userEntities,
        );
    }

    /**
     * Creates a new Project entity.
     *
     * @Route("/create", name="oro_issue_create")
     * @Template("OroIssueBundle:Issue:new.html.twig")
     *
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Issue();
        $user = $this->getUser();
        $entity->setAssignee($user);
        $entity->setCreatedAt();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $issueType = $entity->getIssueType()->getCode();
            if ($issueType != IssueTypeEntity::TYPE_SUBTASK) {
                $entity->setParent(null);
            }
            $entity->addCollaborator($user);
            $entity->addCollaborator($entity->getAssignee());
            $entity->setReporter($user);
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('oro_issue_view', array('id' => $entity->getId())));
        }

        return array(
            'user' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Issue entity.
     *
     * @param Issue $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Issue $entity)
    {
        $form = $this->createForm(new IssueType($this->getUser()), $entity);
        $form->add('submit', 'submit', array('label' => 'Create'));
        return $form;
    }

    /**
     * Finds and displays a Issue entity.
     *
     * @Route("/view/{id}", name="oro_issue_view", requirements={"id"="\d+"})
     * @ParamConverter("entity", class="OroIssueBundle:Issue")
     * @Template()
     *
     * @param Issue $entity
     * @return array
     */
    public function viewAction(Issue $entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('issue.messages.entity_not_found'));
        }
        $deleteForm = $this->createDeleteForm($entity);
        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Issue entity.
     *
     * @param Issue $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Issue $entity)
    {
        $form = $this->createForm(new IssueType($this->getUser()), $entity);
        $form->add('submit', 'submit', array('label' => 'Update'));
        return $form;
    }

    /**
     * Edits an existing Project entity.
     *
     * @Route("/update/{id}", name="oro_issue_update", requirements={"id"="\d+"})
     * @ParamConverter("entity", class="OroIssueBundle:Issue")
     * @Template("OroIssueBundle:Issue:edit.html.twig")
     *
     * @param Issue $entity
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws AccessDeniedException
     */
    public function updateAction(Issue $entity, Request $request)
    {
        $errors = array();
        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('issue.messages.entity_not_found'));
        }
        if (false === $this->get('security.authorization_checker')->isGranted('ACCESS', $entity)) {
            throw new AccessDeniedException();
        }

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $issueType = $entity->getIssueType()->getCode();
            if ($issueType != IssueTypeEntity::TYPE_SUBTASK) {
                $entity->setParent(null);
            }
            $entity->setUpdatedAt();
            $entity->addCollaborator($entity->getAssignee());
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirect($this->generateUrl('oro_issue_view', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errors' => $errors
        );
    }

    /**
     * Deletes a Issue entity.
     *
     * @Route("/delete/{id}", name="oro_issue_delete", requirements={"id"="\d+"})
     * @ParamConverter("entity", class="OroIssueBundle:Issue")
     * @Method("DELETE")
     *
     * @param Issue $entity
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Issue $entity, Request $request)
    {
        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OroIssueBundle:Issue')->find($entity->getId());
            if (!$entity) {
                throw $this->createNotFoundException(
                    $this->get('translator')->trans('issue.messages.entity_not_found')
                );
            }
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('oro_issue'));
    }

    /**
     * Creates a form to delete a Issue entity by id.
     *
     * @param Issue $entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Issue $entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('oro_issue_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}
