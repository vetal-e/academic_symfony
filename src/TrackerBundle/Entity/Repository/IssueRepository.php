<?php

namespace TrackerBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use TrackerBundle\Entity\Activity;
use TrackerBundle\Entity\Issue;
use TrackerBundle\Entity\Project;
use TrackerBundle\Entity\User;

/**
 * IssueRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class IssueRepository extends EntityRepository implements ActivitiesGettableInterface
{
    /**
     * @param Project $project
     * @return Issue[]
     */
    public function getRootProjectIssues(Project $project)
    {
        $rootProjectIssues = $this->findBy(
            [
                'project' => $project,
                'parentIssue' => null,
            ],
            [
                'updatedAt' => 'DESC',
            ]
        );

        return $rootProjectIssues;
    }

    /**
     * @param User $user
     * @return Issue[]
     */
    public function getUserAssignedIssues(User $user)
    {
        $issues = $this->createQueryBuilder('i')
            ->innerJoin('i.assignee', 'u')
            ->where('u.id = :userId')
            ->andWhere('i.status <> :statusClosed or i.status is null')
            ->andWhere('i.resolution <> :resolutionResolved or i.resolution is null')
            ->setParameters([
                'userId' => $user->getId(),
                'statusClosed' => 'STATUS_CLOSED',
                'resolutionResolved' => 'RESOLUTION_RESOLVED',
            ])
            ->orderBy('i.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
        ;

        return $issues;
    }

    /**
     * @param Issue $issue
     * @return Activity[]
     */
    public function getActivities($issue)
    {
        $activityRepository = $this->getEntityManager()->getRepository('TrackerBundle:Activity');
        $activities = $activityRepository->createQueryBuilder('a')
            ->innerJoin('a.issue', 'i')
            ->where('i.id = :issueId')
            ->setParameters([
                'issueId' => $issue->getId(),
            ])
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $activities;
    }
}
