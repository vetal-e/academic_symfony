<?php

namespace TrackerBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use TrackerBundle\Entity\Activity;
use TrackerBundle\Entity\Project;

class ProjectRepository extends EntityRepository
{
    /**
     * @param Project $project
     * @return Activity[]
     */
    public function getProjectActivities(Project $project)
    {
        $activityRepository = $this->getEntityManager()->getRepository('TrackerBundle:Activity');
        $activities = $activityRepository->createQueryBuilder('a')
            ->innerJoin('a.issue', 'i')
            ->innerJoin('i.project', 'p')
            ->where('p.id = :projectId')
            ->setParameters([
                'projectId' => $project->getId(),
            ])
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $activities;
    }
}
