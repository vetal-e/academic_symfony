<?php

namespace TrackerBundle\Entity\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use TrackerBundle\Entity\Activity;
use TrackerBundle\Entity\Comment;
use TrackerBundle\Entity\User;

class CommentListener
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
     * @param Comment $comment
     * @param PreFlushEventArgs $event
     */
    public function preFlush(Comment $comment, PreFlushEventArgs $event)
    {
        $issue = $comment->getIssue();
        $collaborator = $comment->getAuthor();

        if ($collaborator instanceof User and !$issue->getCollaborators()->contains($collaborator)) {
            $issue->addCollaborator($collaborator);
        }
    }

    /**
     * @param Comment $comment
     * @param LifecycleEventArgs $event
     */
    public function postPersist(Comment $comment, LifecycleEventArgs $event)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        $activity = new Activity();
        $activity->setType('TYPE_ISSUE_COMMENT');
        $activity->setIssue($comment->getIssue());
        $activity->setComment($comment);
        $activity->setUser($user);
        $activity->setContent(
            "%username_url% added new %comment_url% in issue %issue_url%"
        );

        $entityManager = $event->getEntityManager();
        $entityManager->persist($activity);
        $entityManager->flush();
    }

    /**
     * @param Comment $comment
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(Comment $comment, PreUpdateEventArgs $event)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($event->hasChangedField('body')) {
            $activity = new Activity();
            $activity->setType('TYPE_ISSUE_COMMENT_UPDATED');
            $activity->setIssue($comment->getIssue());
            $activity->setComment($comment);
            $activity->setUser($user);
            $activity->setContent(
                "%username_url% updated the %comment_url% in issue %issue_url%"
            );

            $this->entitiesToFlush[] = $activity;
        }
    }

    /**
     * @param Comment $comment
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(Comment $comment, LifecycleEventArgs $event)
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