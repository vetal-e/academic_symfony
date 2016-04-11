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
     * @Route("/user/view/{userId}", name="user_view", requirements={"userId": "\d+"})
     * @Method({"GET"})
     * @Template("user/view.html.twig")
     *
     * @param number $userId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($userId = null)
    {
        /** @var User $user */
        if (empty($userId)) {
            $user = $this->getUser();
        } else {
            $user = $this->getDoctrine()->getRepository('TrackerBundle:User')->findOneById($userId);
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
     * @Route("/user/edit/{userId}", name="user_edit", requirements={"userId": "\d+"})
     * @Method({"GET", "POST"})
     * @Template("user/edit.html.twig")
     *
     * @param number $userId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $userId = null)
    {
        /** @var User $user */
        if (empty($userId)) {
            $user = $this->getUser();
        } else {
            $user = $this->getDoctrine()->getRepository('TrackerBundle:User')->findOneById($userId);
        }

        if (empty($user)) {
            throw $this->createNotFoundException('User not found');
        }

        $form = $this->createForm(new UserType(), $user, ['label' => 'Edit user info']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_view', ['userId' => $user->getId()]);
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
