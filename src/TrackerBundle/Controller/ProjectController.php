<?php

namespace TrackerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TrackerBundle\Entity\Project;
use TrackerBundle\Form\ProjectType;

class ProjectController extends Controller
{
    /**
     * @Route("/project/view/{id}", name="project_view")
     * @ParamConverter("project", class="TrackerBundle:Project")
     * @Method({"GET"})
     * @Template("project/view.html.twig")
     *
     * @param Project $project
     * @return Response
     */
    public function viewAction($project)
    {
        $this->denyAccessUnlessGranted(
            'view',
            $project,
            'You don\'t have permissions to view this'
        );

        $issueRepository = $this->getDoctrine()->getRepository('TrackerBundle:Issue');
        $activitiesManager = $this->get('tracker.activity.manager');

        $rootProjectIssues = $issueRepository->getRootProjectIssues($project);
        $projectActivities = $activitiesManager->getProjectActivitiesReadable($project);

        return [
            'project' => $project,
            'rootIssues' => $rootProjectIssues,
            'activities' => $projectActivities,
        ];
    }

    /**
     * @Route("/project/create/", name="project_create")
     * @Method({"GET", "POST"})
     * @Template("project/edit.html.twig")
     *
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $project = new Project();

        $this->denyAccessUnlessGranted(
            'create',
            $project,
            'You don\'t have permissions to create projects'
        );

        $form = $this->createForm(new ProjectType(), $project, ['label' => 'Create project']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_view', ['id' => $project->getId()]);
        }

        return [
            'form' => $form->createView(),
            'pageTitle' => 'Create project',
        ];
    }

    /**
     * @Route("/project/edit/{id}", name="project_edit")
     * @ParamConverter("project", class="TrackerBundle:Project")
     * @Method({"GET", "POST"})
     * @Template("project/edit.html.twig")
     *
     * @param Request $request
     * @param Project $project
     * @return Response
     */
    public function editAction(Request $request, Project $project)
    {
        $this->denyAccessUnlessGranted(
            'edit',
            $project,
            'You don\'t have permissions to edit this project'
        );

        $form = $this->createForm(new ProjectType(), $project, ['label' => 'Edit project']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('project_view', ['id' => $project->getId()]);
        }

        return [
            'form' => $form->createView(),
            'pageTitle' => 'Edit project',
        ];
    }
}