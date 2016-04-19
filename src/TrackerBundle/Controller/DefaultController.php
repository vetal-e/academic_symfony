<?php

namespace TrackerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use TrackerBundle\Entity\Activity;
use TrackerBundle\Entity\Issue;
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
        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getRepository('TrackerBundle:User');
        $userProjects = $userRepository->getUserProjects($user);
        $userIssues = $userRepository->getUserIssues($user);
        $userActivities = $userRepository->getUserActivities($user);
        $userActivities = $this->replaceActivityPlaceholders($userActivities);

        return [
            'projects' => $userProjects,
            'issues' => $userIssues,
            'activities' => $userActivities,
        ];
    }

    /**
     * @param Activity[] $activities
     *
     * @return Activity[]
     */
    private function replaceActivityPlaceholders($activities)
    {
        foreach ($activities as $activity) {
            $values = [
                '%username_url%' => '<a href="' . $this->generateUrl('user_view', ['id' => $activity->getUser()->getId()]) . '">' . $activity->getUser()->getUsername() . '</a>',
                '%issue_url%' => '<a href="' . $this->generateUrl('issue_view', ['id' => $activity->getIssue()->getId()]) . '">' . $activity->getIssue()->getCode() . '</a>',
                '%STATUS_OPEN%' => Issue::STATUS_OPEN,
                '%STATUS_IN_PROGRESS%' => Issue::STATUS_IN_PROGRESS,
                '%STATUS_CLOSED%' => Issue::STATUS_CLOSED,
                '%PRIORITY_LOW%' => Issue::PRIORITY_LOW,
                '%PRIORITY_NORMAL%' => Issue::PRIORITY_NORMAL,
                '%PRIORITY_HIGH%' => Issue::PRIORITY_HIGH,
                '%PRIORITY_URGENT%' => Issue::PRIORITY_URGENT,
                '%RESOLUTION_RESOLVED%' => Issue::RESOLUTION_RESOLVED,
                '%RESOLUTION_REOPENED%' => Issue::RESOLUTION_REOPENED,
                '%%' => 'None',
                '%comment_url%' => 'comment',
            ];

            foreach ($values as $key => $value) {
                $activity->setContent(str_replace($key, $value, $activity->getContent()));
            }
        }

        return $activities;
    }
}
