<?php

namespace TrackerBundle\Entity\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use TrackerBundle\Entity\Activity;
use TrackerBundle\Manager\ActivityManager;
use TrackerBundle\Manager\MailManager;

class ActivityListener
{
    protected $activityManager;
    protected $mailManager;

    /**
     * @param ActivityManager $activityManager
     * @param MailManager $mailManager
     */
    public function __construct(ActivityManager $activityManager, MailManager $mailManager)
    {
        $this->activityManager = $activityManager;
        $this->mailManager = $mailManager;
    }
    /**
     * @param Activity $activity
     * @param LifecycleEventArgs $event
     */
    public function postPersist(Activity $activity, LifecycleEventArgs $event)
    {
        $activity = $this->activityManager->replaceActivityPlaceholders($activity, $absolutePaths = true);

        $collaborators = $activity->getIssue()->getCollaborators();

        foreach ($collaborators as $collaborator) {
            $this->mailManager->sendActivityEmail($collaborator, $activity);
        }
    }
}
