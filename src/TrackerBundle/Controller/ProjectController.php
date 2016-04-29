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
use TrackerBundle\Entity\User;
use TrackerBundle\Form\ProjectMembersAddType;
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

        $rootProjectIssues = $issueRepository->getRootProjectIssues($project);

        return [
            'project' => $project,
            'rootIssues' => $rootProjectIssues,
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
            'project' => $project,
        ];
    }

    /**
     * @Route("/project/{id}/members/add", name="project_members_add")
     * @ParamConverter("project", class="TrackerBundle:Project")
     * @Method({"GET", "POST"})
     * @Template("project/membersAdd.html.twig")
     *
     * @param Request $request
     * @param Project $project
     * @return Response
     */
    public function addMembersAction(Request $request, Project $project)
    {
        $this->denyAccessUnlessGranted(
            'manage_members',
            $project,
            'You don\'t have permissions to edit this project'
        );

        $userRepository = $this->getDoctrine()->getRepository('TrackerBundle:User');
        $allUsers = $userRepository->findBy([], ['id' => 'DESC']);

        $form = $this->createForm(new ProjectMembersAddType(), $project, ['label' => 'Add members to the project']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $newMembers = [];
            $existingMembers = $project->getMembers();

            $data = $request->request->get('project');
            if (isset($data['newMembers'])) {
                $newMembers = $data['newMembers'];
            }
            foreach ($newMembers as $newMemberId) {
                $newMember = $userRepository->find($newMemberId);
                if (!empty($newMember) and !$existingMembers->contains($newMember)) {
                    $project->addMember($newMember);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('project_edit', ['id' => $project->getId()]);
        }

        return [
            'form' => $form->createView(),
            'pageTitle' => 'Add members',
            'project' => $project,
            'users' => $allUsers,
        ];
    }

    /**
     * @Route("/project/{project_id}/member/remove/{user_id}", name="project_member_remove")
     * @ParamConverter("project", class="TrackerBundle:Project", options={"id" = "project_id"})
     * @ParamConverter("user", class="TrackerBundle:User", options={"id" = "user_id"})
     * @Method({"GET", "POST"})
     *
     * @param Project $project
     * @param User $user
     * @return Response
     */
    public function removeMemberAction(Project $project, User $user)
    {
        $this->denyAccessUnlessGranted(
            'manage_members',
            $project,
            'You don\'t have permissions to edit this project'
        );

        $entityManager = $this->getDoctrine()->getManager();
        $project->removeMember($user);
        $entityManager->flush();

        return $this->redirectToRoute('project_edit', ['id' => $project->getId()]);
    }
}