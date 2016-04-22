<?php

namespace TrackerBundle\Security\Authorization\Voter;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use TrackerBundle\Entity\User;

class UserVoter extends AbstractVoter
{
    const EDIT = 'edit';
    const CHANGE_ROLES = 'change_roles';
    const CREATE_PROJECT = 'create_project';

    protected function getSupportedAttributes()
    {
        return array(self::EDIT, self::CHANGE_ROLES, self::CREATE_PROJECT);
    }

    protected function getSupportedClasses()
    {
        return array('TrackerBundle\Entity\User');
    }

    /**
     * @param string $attribute
     * @param User $userObject
     * @param User $user
     * @return bool
     */
    protected function isGranted($attribute, $userObject, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$user instanceof User) {
            throw new \LogicException('The user is of unsupported class');
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        switch ($attribute) {
            case self::EDIT:
                if ($userObject == $user) {
                    return true;
                }
                break;
            case self::CHANGE_ROLES:
                if (in_array('ROLE_ADMIN', $user->getRoles())) {
                    return true;
                }
                break;
            case self::CREATE_PROJECT:
                if (in_array('ROLE_MANAGER', $user->getRoles()) or in_array('ROLE_ADMIN', $user->getRoles())) {
                    return true;
                }
                break;
        }

        return false;
    }
}
