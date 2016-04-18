<?php

namespace TrackerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method({"GET"})
     * @Template("default/home.html.twig")
     *
     * @return Response
     */
    public function indexAction()
    {
        $userRepository = $this->getDoctrine()->getRepository('TrackerBundle:User');
        $userProjects = $userRepository->getUserProjects($this->getUser());

        return [
            'projects' => $userProjects,
        ];
    }
}
