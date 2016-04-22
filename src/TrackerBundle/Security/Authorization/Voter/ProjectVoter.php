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
    const CREATE_ISSUE = 'create_issue';
    const MANAGE_MEMBERS = 'manage_members';

    protected function getSupportedAttributes()
    {
        return array(self::VIEW, self::CREATE, self::EDIT, self::CREATE_ISSUE, self::MANAGE_MEMBERS);
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

        if (in_array('ROLE_MANAGER', $user->getRoles()) or in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        switch ($attribute) {
            case self::VIEW:
                if (in_array('ROLE_OPERATOR', $user->getRoles()) and $project->getMembers()->contains($user)) {
                    return true;
                }
                break;
            case self::CREATE_ISSUE:
                if (in_array('ROLE_OPERATOR', $user->getRoles()) and $project->getMembers()->contains($user)) {
                    return true;
                }
                break;
        }

        return false;
    }
}
