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

        return [
            'user' => $user,
            'roles' => implode(', ', $user->getRoleNames()),
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

        $form = $this->createForm(new UserType(), $user, ['label' => 'Edit user info']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_view', ['id' => $user->getId()]);
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
