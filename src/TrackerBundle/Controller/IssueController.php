<?php

namespace TrackerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TrackerBundle\Entity\Issue;
use TrackerBundle\Form\IssueType;

class IssueController extends Controller
{
    /**
     * @Route("/issue/view/{id}", name="issue_view")
     * @ParamConverter("issue", class="TrackerBundle:Issue")
     * @Method({"GET"})
     * @Template("issue/view.html.twig")
     *
     * @param Issue $issue
     * @return Response
     */
    public function viewAction($issue)
    {
        return [
            'issue' => $issue,
        ];
    }

    /**
     * @Route("/issue/create/", name="issue_create")
     * @Method({"GET", "POST"})
     * @Template("issue/edit.html.twig")
     *
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $issue = new Issue();
        $form = $this->createForm(new IssueType(), $issue, ['label' => 'New issue']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($issue);
            $entityManager->flush();

            return $this->redirectToRoute('issue_view', ['id' => $issue->getId()]);
        }

        return [
            'form' => $form->createView(),
            'pageTitle' => 'New issue',
        ];
    }

    /**
     * @Route("/issue/edit/{id}", name="issue_edit")
     * @ParamConverter("issue", class="TrackerBundle:Issue")
     * @Method({"GET", "POST"})
     * @Template("issue/edit.html.twig")
     *
     * @param Request $request
     * @param Issue $issue
     * @return Response
     */
    public function editAction(Request $request, Issue $issue)
    {
        $form = $this->createForm(new IssueType('edit'), $issue, ['label' => 'Edit issue']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('issue_view', ['id' => $issue->getId()]);
        }

        return [
            'form' => $form->createView(),
            'pageTitle' => 'Edit issue',
        ];
    }
}