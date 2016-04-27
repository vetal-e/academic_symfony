<?php

namespace TrackerBundle\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use TrackerBundle\Entity\Activity;
use TrackerBundle\Entity\Issue;
use TrackerBundle\Entity\Project;
use TrackerBundle\Entity\User;

class ActivityManager
{
    protected $doctrine;
    protected $router;

    /**
     * @param Registry $doctrine
     * @param Router $router
     */
    public function __construct(Registry $doctrine, Router $router)
    {
        $this->doctrine = $doctrine;
        $this->router = $router;
    }

    /**
     * @param Project $project
     * @return Activity[]
     */
    public function getProjectActivitiesReadable(Project $project)
    {
        $activities = $this->doctrine->getRepository('TrackerBundle:Project')->getProjectActivities($project);
        $activitiesReadable = $this->replaceActivityPlaceholders($activities);

        return $activitiesReadable;
    }

    /**
     * @param User $user
     * @return Activity[]
     */
    public function getUserActivitiesReadable(User $user)
    {
        $activities = $this->doctrine->getRepository('TrackerBundle:User')->getUserActivities($user);
        $activitiesReadable = $this->replaceActivityPlaceholders($activities);

        return $activitiesReadable;
    }

    /**
     * @param Issue $issue
     * @return Activity[]
     */
    public function getIssueActivitiesReadable($issue)
    {
        $activities = $this->doctrine->getRepository('TrackerBundle:Issue')->getIssueActivities($issue);
        $activitiesReadable = $this->replaceActivityPlaceholders($activities);

        return $activitiesReadable;
    }

    /**
     * @param Activity|Activity[] $activity
     * @param bool $absolutePaths
     * @return Activity
     */
    public function replaceActivityPlaceholders($activity, $absolutePaths = false)
    {
        if (is_array($activity)) {
            return $this->replaceActivityArrayPlaceholders($activity, $absolutePaths);
        }

        $userUrl = $this->router->generate('user_view', ['id' => $activity->getUser()->getId()], $absolutePaths);
        $issueUrl = $this->router->generate('issue_view', ['id' => $activity->getIssue()->getId()], $absolutePaths);

        if (!empty($activity->getComment())) {
            $commentAnchor = '#comment-' . $activity->getComment()->getId();
        } else {
            $commentAnchor = '';
        }

        $values = [
            '%username_url%' => '<a href="' . $userUrl . '">' . $activity->getUser()->getUsername() . '</a>',
            '%issue_url%' => '<a href="' . $issueUrl . '">' . $activity->getIssue()->getCode() . '</a>',
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
            '%comment_url%' => '<a href="' . $issueUrl . $commentAnchor . '">comment</a>',
        ];

        $activity->setContentReadable($activity->getContent());
        foreach ($values as $key => $value) {
            $activity->setContentReadable(str_replace($key, $value, $activity->getContentReadable()));
        }

        return $activity;
    }

    /**
     * @param Activity[] $activities
     * @param bool $absolutePaths
     * @return Activity[]
     */
    protected function replaceActivityArrayPlaceholders($activities, $absolutePaths)
    {
        $result = [];
        foreach ($activities as $activity) {
            if ($activity instanceof Activity) {
                $result[] = $this->replaceActivityPlaceholders($activity, $absolutePaths);
            }
        }

        return $result;
    }
}