<?php

namespace TrackerBundle\Security\Authorization\Voter;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use TrackerBundle\Entity\User;

class UserVoter extends AbstractVoter
{
    const EDIT = 'edit';

    protected function getSupportedAttributes()
    {
        return array(self::EDIT);
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

        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            case self::EDIT:
                if ($userObject == $user) {
                    return true;
                }
                break;
        }

        return false;
    }
}
