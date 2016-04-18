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
}
