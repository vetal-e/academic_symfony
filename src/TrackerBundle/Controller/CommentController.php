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
     * @param $issue
     * @return Response
     */
    public function createAction(Request $request, $issue)
    {
        $comment = new Comment();
        $comment->setIssue($issue);
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
}