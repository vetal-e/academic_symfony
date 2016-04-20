<?php

namespace TrackerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use TrackerBundle\Entity\Repository\UserRepository;

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
        $user = $this->getUser();
        $userRepository = $this->getDoctrine()->getRepository('TrackerBundle:User');
        $activitiesManager = $this->get('tracker.activity.manager');

        $userProjects = $userRepository->getUserProjects($user);
        $userIssues = $userRepository->getUserIssues($user);
        $userActivities = $activitiesManager->getUserActivitiesReadable($user);

        return [
            'projects' => $userProjects,
            'issues' => $userIssues,
            'activities' => $userActivities,
        ];
    }
}
