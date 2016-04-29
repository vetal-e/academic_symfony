<?php

namespace TrackerBundle\Entity\Repository;

use TrackerBundle\Entity\Activity;

interface ActivitiesGettableInterface
{
    /**
     * @param object $object
     * @return Activity[]
     */
    public function getActivities($object);
}
