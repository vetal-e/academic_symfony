<?php

namespace TrackerBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use TrackerBundle\Entity\User;

class UserRepository extends EntityRepository
{
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
}
