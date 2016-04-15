<?php

namespace TrackerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TrackerBundle\Entity\Comment;
use TrackerBundle\Entity\Issue;
use TrackerBundle\Form\CommentType;

class CommentController extends Controller
{
    /**
     * @Route("/issue/{id}/comment/create/", name="comment_create")
     * @ParamConverter("issue", class="TrackerBundle:Issue")
     * @Method({"GET", "POST"})
     * @Template("comment/edit.html.twig")
     *
     * @param Request $request
     * @param Issue $issue
     * @return Response
     */
    public function createAction(Request $request, Issue $issue)
    {
        $comment = new Comment();
        $comment->setIssue($issue);

        $this->denyAccessUnlessGranted(
            'create',
            $comment,
            'You have to be a member of the project to be able to comment'
        );

        $comment->setAuthor($this->getUser());

        $form = $this->createForm(new CommentType(), $comment, ['label' => 'Add comment']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('issue_view', ['id' => $issue->getId()]);
        }

        return [
            'form' => $form->createView(),
            'pageTitle' => 'Add comment',
        ];
    }

    /**
     * @Route("/comment/edit/{id}", name="comment_edit")
     * @ParamConverter("comment", class="TrackerBundle:Comment")
     * @Method({"GET", "POST"})
     * @Template("comment/edit.html.twig")
     *
     * @param Request $request
     * @param Comment $comment
     * @return Response
     */
    public function editAction(Request $request, Comment $comment)
    {
        $this->denyAccessUnlessGranted('edit', $comment, 'You cannot edit other people\'s comments');

        $form = $this->createForm(new CommentType('edit'), $comment, ['label' => 'Edit comment']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('issue_view', ['id' => $comment->getIssue()->getId()]);
        }

        return [
            'form' => $form->createView(),
            'pageTitle' => 'Edit comment',
        ];
    }

    /**
     * @Route("/comment/delete/{id}", name="comment_delete")
     * @ParamConverter("comment", class="TrackerBundle:Comment")
     * @Method({"GET", "POST"})
     *
     * @param Comment $comment
     * @return Response
     */
    public function deleteAction(Comment $comment)
    {
        $this->denyAccessUnlessGranted('delete', $comment, 'You cannot delete other people\'s comments');

        $issue = $comment->getIssue();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirectToRoute('issue_view', ['id' => $issue->getId()]);
    }
}