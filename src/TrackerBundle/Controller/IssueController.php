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
use TrackerBundle\Entity\Project;
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
        $this->denyAccessUnlessGranted(
            'view',
            $issue,
            'You don\'t have permissions to view this'
        );

        return [
            'issue' => $issue,
        ];
    }

    /**
     * @Route("/project/{id}/issue/create/", name="issue_create")
     * @ParamConverter("project", class="TrackerBundle:Project")
     * @Method({"GET", "POST"})
     * @Template("issue/edit.html.twig")
     *
     * @param Request $request
     * @param Project $project
     * @return Response
     */
    public function createAction(Request $request, Project $project)
    {
        $issue = new Issue();
        $issue->setProject($project);
        $issue->setReporter($this->getUser());

        $this->denyAccessUnlessGranted(
            'create',
            $issue,
            'You have to be a member of the project to be able to create issues'
        );

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
        $this->denyAccessUnlessGranted(
            'edit',
            $issue,
            'You have to be a member of the project to be able to edit this issue'
        );

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
