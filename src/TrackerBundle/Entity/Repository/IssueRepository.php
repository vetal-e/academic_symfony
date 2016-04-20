<?php

namespace TrackerBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use TrackerBundle\Entity\Activity;
use TrackerBundle\Entity\Issue;
use TrackerBundle\Entity\Project;

/**
 * IssueRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class IssueRepository extends EntityRepository
{
    /**
     * @param Project $project
     * @return Issue[]
     */
    public function getRootProjectIssues(Project $project)
    {
        $rootProjectIssues = $this->findBy([
            'project' => $project,
            'parentIssue' => null,
        ]);

        return $rootProjectIssues;
    }

    /**
     * @param Issue $issue
     * @return Activity[]
     */
    public function getIssueActivities(Issue $issue)
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
