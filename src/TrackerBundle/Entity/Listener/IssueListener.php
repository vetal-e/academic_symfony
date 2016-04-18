<?php

namespace TrackerBundle\Entity\Listener;

use Doctrine\ORM\Event\PreFlushEventArgs;
use TrackerBundle\Entity\Issue;
use TrackerBundle\Entity\User;

class IssueListener
{
    public function preFlush(Issue $issue, PreFlushEventArgs $event)
    {
        $collaborators = [];
        $collaborators[] = $issue->getReporter();
        $collaborators[] = $issue->getAssignee();

        foreach ($collaborators as $collaborator) {
            if ($collaborator instanceof User and !$issue->getCollaborators()->contains($collaborator)) {
                $issue->addCollaborator($collaborator);
            }
        }
    }
}