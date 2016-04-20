<?php

namespace TrackerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TrackerBundle\Form\UserType;
use TrackerBundle\Entity\User;

class UserController extends Controller
{
    /**
     * @Route("/user/view/{id}", name="user_view", requirements={"id": "\d+"})
     * @Method({"GET"})
     * @Template("user/view.html.twig")
     *
     * @param number $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($id = null)
    {
        /** @var User $user */
        if (empty($id)) {
            $user = $this->getUser();
        } else {
            $user = $this->getDoctrine()->getRepository('TrackerBundle:User')->findOneById($id);
        }

        if (empty($user)) {
            throw $this->createNotFoundException('User not found');
        }

        $activitiesManager = $this->get('tracker.activity.manager');
        $userActivities = $activitiesManager->getUserActivitiesReadable($user);

        return [
            'user' => $user,
            'roles' => implode(', ', $user->getRoleNames()),
            'activities' => $userActivities,
        ];
    }

    /**
     * @Route("/user/edit/{id}", name="user_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Template("user/edit.html.twig")
     *
     * @param number $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id = null)
    {
        /** @var User $user */
        if (empty($id)) {
            $user = $this->getUser();
        } else {
            $user = $this->getDoctrine()->getRepository('TrackerBundle:User')->findOneById($id);
        }

        if (empty($user)) {
            throw $this->createNotFoundException('User not found');
        }

        $this->denyAccessUnlessGranted(
            'edit',
            $user,
            'You don\'t have permissions to edit this user'
        );

        $canChangeRoles = false;
        $rolesStash = $user->getRoles();
        if ($this->isGranted('change_roles', $user)) {
            $canChangeRoles = true;
        }

        $form = $this->createForm(new UserType($canChangeRoles), $user, ['label' => 'Edit user info']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$canChangeRoles) {
                $user->setRoles($rolesStash);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_view', ['id' => $user->getId()]);
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
