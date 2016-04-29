<?php

namespace TrackerBundle\Twig;

use TrackerBundle\Entity\ActivitiesGettableEntityInterface;
use TrackerBundle\Entity\Activity;
use TrackerBundle\Manager\ActivityManager;

class ActivitiesExtension extends \Twig_Extension
{
    private $activityManager;

    public function __construct(ActivityManager $activityManager)
    {
        $this->activityManager = $activityManager;
    }

    public function getName()
    {
        return 'activities_extension';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('getActivities', [$this, 'getActivities']),
        ];
    }

    /**
     * @param ActivitiesGettableEntityInterface $object
     * @return Activity[]
     */
    public function getActivities(ActivitiesGettableEntityInterface $object)
    {
        return $this->activityManager->getActivitiesReadable($object);
    }
}
