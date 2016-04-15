<?php

namespace TrackerBundle\Security\Authorization\Voter;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use TrackerBundle\Entity\Comment;
use TrackerBundle\Entity\User;

class CommentVoter extends AbstractVoter
{
    const CREATE = 'create';
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function getSupportedAttributes()
    {
        return array(self::CREATE, self::EDIT, self::DELETE);
    }

    protected function getSupportedClasses()
    {
        return array('TrackerBundle\Entity\Comment');
    }

    /**
     * @param string $attribute
     * @param Comment $comment
     * @param User $user
     * @return bool
     */
    protected function isGranted($attribute, $comment, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$user instanceof User) {
            throw new \LogicException('The user is of unsupported class');
        }

        switch ($attribute) {
            case self::CREATE:
                if ($comment->getIssue()->getProject()->getMembers()->contains($user)) {
                    return true;
                }
                break;
            case self::EDIT:
                if ($user->getId() === $comment->getAuthor()->getId()) {
                    return true;
                }
                break;
            case self::DELETE:
                if ($user->getId() === $comment->getAuthor()->getId()) {
                    return true;
                }
                break;
        }

        return false;
    }
}
