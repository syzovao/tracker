<?php

namespace Oro\IssueBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Oro\IssueBundle\Entity\Issue;
use Oro\IssueBundle\Entity\IssueComment;
use Oro\IssueBundle\Form\IssueCommentType;

/**
 * IssueComment controller.
 *
 * @Route("/comment")
 */
class CommentController extends Controller
{

    /**
     * Lists all IssueComment entities.
     *
     * @Route("/", name="oro_comment")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OroIssueBundle:IssueComment')->findAll();
        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new IssueComment entity.
     *
     * @Route("/create/{issueId}", name="oro_comment_create")
     * @ParamConverter("issue", class="OroIssueBundle:Issue", options={"id" = "issueId"})
     * @Template("OroIssueBundle:Comment:new.html.twig")
     *
     * @param Issue $issue
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Issue $issue, Request $request)
    {
        $entity = new IssueComment();
        $entity->setIssue($issue);
        $entity->setCreatedAt();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('oro_issue_view', array('id' => $issue->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a IssueComment entity.
     *
     * @param IssueComment $entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(IssueComment $entity)
    {
        $url = $this->generateUrl('oro_comment_create', array('issueId' => $entity->getIssue()->getId()));
        $form = $this->createForm(new IssueCommentType(), $entity, array(
            'action' => $url,
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Create'));
        return $form;
    }

    /**
    * Creates a form to edit a IssueComment entity.
    *
    * @param IssueComment $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(IssueComment $entity)
    {
        $form = $this->createForm(new IssueCommentType(), $entity, array(
            'action' => $this->generateUrl('oro_comment_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        $form->add('submit', 'submit', array('label' => 'Update'));
        return $form;
    }

    /**
     * Edits an existing IssueComment entity.
     *
     * @Route("/update/{id}", name="oro_comment_update")
     * @ParamConverter("entity", class="OroIssueBundle:IssueComment")
     * @Template("OroIssueBundle:Comment:edit.html.twig")
     *
     * @param IssueComment $entity
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws AccessDeniedException
     */
    public function updateAction(IssueComment $entity, Request $request)
    {
        if (!$entity) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('issue.messages.entity_comment_not_found')
            );
        }

        if (!$this->get('security.authorization_checker')->isGranted('EDIT', $entity)) {
            throw $this->createAccessDeniedException('issue.validators.comment.permissions_denied_edit');
        }

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirect($this->generateUrl('oro_issue_view', array('id' => $entity->getIssue()->getId())));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a IssueComment entity.
     *
     * @Route("/delete/{id}", name="oro_comment_delete", requirements={"id"="\d+"})
     * @ParamConverter("entity", class="OroIssueBundle:IssueComment")
     *
     * @param IssueComment $entity
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws AccessDeniedException
     */
    public function deleteAction(IssueComment $entity)
    {
        if (!$this->get('security.authorization_checker')->isGranted('DELETE', $entity)) {
            throw $this->createAccessDeniedException('issue.validators.comment.permissions_denied_delete');
        }

        $issueId = $entity->getIssue()->getId();
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('oro_issue_view', array('id' => $issueId)));
    }

    /**
     * Creates a form to delete a IssueComment entity by id.
     *
     * @param IssueComment $entity
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm(IssueComment $entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('oro_comment_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}
