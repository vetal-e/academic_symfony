<?php

namespace TrackerBundle\Security\Authorization\Voter;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use TrackerBundle\Entity\Project;
use TrackerBundle\Entity\User;

class ProjectVoter extends AbstractVoter
{
    const VIEW = 'view';
    const CREATE = 'create';
    const EDIT = 'edit';

    protected function getSupportedAttributes()
    {
        return array(self::VIEW, self::CREATE, self::EDIT);
    }

    protected function getSupportedClasses()
    {
        return array('TrackerBundle\Entity\Project');
    }

    /**
     * @param string $attribute
     * @param Project $project
     * @param User $user
     * @return bool
     */
    protected function isGranted($attribute, $project, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$user instanceof User) {
            throw new \LogicException('The user is of unsupported class');
        }

        if ($user->hasRole('ROLE_MANAGER') or $user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            case self::VIEW:
                if ($user->hasRole('ROLE_OPERATOR') and $project->getMembers()->contains($user)) {
                    return true;
                }
                break;
        }

        return false;
    }
}
