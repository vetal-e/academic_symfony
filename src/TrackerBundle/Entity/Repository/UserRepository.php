<?php

namespace TrackerBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use TrackerBundle\Entity\Activity;
use TrackerBundle\Entity\Issue;
use TrackerBundle\Entity\Project;
use TrackerBundle\Entity\User;

class UserRepository extends EntityRepository implements ActivitiesGettableInterface
{
    /**
     * @param User $user
     * @return Project[]
     */
    public function getUserProjects(User $user)
    {
        if ($user->hasRole('ROLE_MANAGER') or $user->hasRole('ROLE_ADMIN')) {
            $projectRepository = $this->getEntityManager()->getRepository('TrackerBundle:Project');
            $projects = $projectRepository->findAll();
        } else {
            $projects = $user->getProjects();
        }

        return $projects;
    }

    /**
     * @param User $user
     * @return Issue[]
     */
    public function getUserIssues(User $user)
    {
        $issueRepository = $this->getEntityManager()->getRepository('TrackerBundle:Issue');
        $issues = $issueRepository->createQueryBuilder('i')
            ->innerJoin('i.collaborators', 'u')
            ->where('u.id = :userId')
            ->andWhere('i.status <> :statusClosed or i.status is null')
            ->andWhere('i.resolution <> :resolutionResolved or i.resolution is null')
            ->setParameters([
                'userId' => $user->getId(),
                'statusClosed' => 'STATUS_CLOSED',
                'resolutionResolved' => 'RESOLUTION_RESOLVED',
            ])
            ->getQuery()
            ->getResult();

        return $issues;
    }

    /**
     * @param User $user
     * @return Activity[]
     */
    public function getActivities($user)
    {
        $activityRepository = $this->getEntityManager()->getRepository('TrackerBundle:Activity');
        $activities = $activityRepository->createQueryBuilder('a')
            ->innerJoin('a.issue', 'i')
            ->innerJoin('i.collaborators', 'u')
            ->where('u.id = :userId')
            ->setParameters([
                'userId' => $user->getId(),
            ])
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $activities;
    }
}
