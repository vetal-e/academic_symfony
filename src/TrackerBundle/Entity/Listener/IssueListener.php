<?php

namespace TrackerBundle\Entity\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use TrackerBundle\Entity\Activity;
use TrackerBundle\Entity\Issue;
use TrackerBundle\Entity\User;

class IssueListener
{
    protected $tokenStorage;
    protected $entitiesToFlush = [];

    /**
     * @param TokenStorage $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param Issue $issue
     * @param PreFlushEventArgs $event
     */
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

    /**
     * @param Issue $issue
     * @param LifecycleEventArgs $event
     */
    public function postPersist(Issue $issue, LifecycleEventArgs $event)
    {
        if ('cli' === php_sapi_name()) {
            // This relies on the issue reporter set in the fixture
            $user = $issue->getReporter();
        } else {
            $user = $this->tokenStorage->getToken()->getUser();
        }

        $activity = new Activity();
        $activity->setType('TYPE_ISSUE_CREATED');
        $activity->setIssue($issue);
        $activity->setUser($user);
        $activity->setContent(
            "%username_url% created new issue %issue_url%"
        );

        $entityManager = $event->getEntityManager();
        $entityManager->persist($activity);
        $entityManager->flush();
    }

    /**
     * @param Issue $issue
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(Issue $issue, PreUpdateEventArgs $event)
    {
        if ('cli' === php_sapi_name()) {
            // This relies on the issue reporter set in the fixture
            $user = $issue->getReporter();
        } else {
            $user = $this->tokenStorage->getToken()->getUser();
        }

        $checkFields = [
            'status',
            'priority',
            'resolution',
        ];

        foreach ($checkFields as $fieldName) {
            if ($event->hasChangedField($fieldName)) {
                $oldStatus = $event->getOldValue($fieldName);
                $newStatus = $event->getNewValue($fieldName);

                $activity = new Activity();
                $activity->setType('TYPE_ISSUE_STATUS_CHANGED');
                $activity->setIssue($issue);
                $activity->setUser($user);
                $activity->setContent(
                    "%username_url% changed issue %issue_url% $fieldName from %$oldStatus% to %$newStatus%"
                );

                $this->entitiesToFlush[] = $activity;
            }
        }
    }

    /**
     * @param Issue $issue
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(Issue $issue, LifecycleEventArgs $event)
    {
        if (!empty($this->entitiesToFlush)) {
            $entityManager = $event->getEntityManager();
            foreach ($this->entitiesToFlush as $entity) {
                $entityManager->persist($entity);
            }
            $this->entitiesToFlush = [];
            $entityManager->flush();
        }
    }
}