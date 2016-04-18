<?php

namespace TrackerBundle\Entity\Listener;

use Doctrine\ORM\Event\PreFlushEventArgs;
use TrackerBundle\Entity\Comment;
use TrackerBundle\Entity\User;

class CommentListener
{
    public function preFlush(Comment $comment, PreFlushEventArgs $event)
    {
        $issue = $comment->getIssue();
        $collaborator = $comment->getAuthor();

        if ($collaborator instanceof User and !$issue->getCollaborators()->contains($collaborator)) {
            $issue->addCollaborator($collaborator);
        }
    }
}