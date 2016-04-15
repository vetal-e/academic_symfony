<?php

namespace TrackerBundle\Security\Authorization\Voter;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use TrackerBundle\Entity\Issue;
use TrackerBundle\Entity\User;

class IssueVoter extends AbstractVoter
{
    const VIEW = 'view';
    const CREATE = 'create';
    const EDIT = 'edit';
    const COMMENT = 'comment';

    protected function getSupportedAttributes()
    {
        return array(self::VIEW, self::CREATE, self::EDIT, self::COMMENT);
    }

    protected function getSupportedClasses()
    {
        return array('TrackerBundle\Entity\Issue');
    }

    /**
     * @param string $attribute
     * @param Issue $issue
     * @param User $user
     * @return bool
     */
    protected function isGranted($attribute, $issue, $user = null)
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
                if ($user->hasRole('ROLE_OPERATOR') and $issue->getProject()->getMembers()->contains($user)) {
                    return true;
                }
                break;
            case self::CREATE:
                if ($user->hasRole('ROLE_OPERATOR') and $issue->getProject()->getMembers()->contains($user)) {
                    return true;
                }
                break;
            case self::EDIT:
                if ($user->hasRole('ROLE_OPERATOR') and $issue->getProject()->getMembers()->contains($user)) {
                    return true;
                }
                break;
            case self::COMMENT:
                if ($issue->getProject()->getMembers()->contains($user)) {
                    return true;
                }
                break;
        }

        return false;
    }
}
