<?php

namespace TrackerBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use TrackerBundle\Entity\Activity;
use TrackerBundle\Entity\Listener\CommentListener;

class CommentListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testPreFlush()
    {
        $commentWithoutCollaborators = $this->getMock('TrackerBundle\Entity\Comment');
        $commentWithCollaborators = $this->getMock('TrackerBundle\Entity\Comment');
        $issueWithoutCollaborators = $this->getMock('TrackerBundle\Entity\Issue');
        $issueWithCollaborators = $this->getMock('TrackerBundle\Entity\Issue');

        $author = $this->getMock('TrackerBundle\Entity\User');

        $emptyCollaborators = new ArrayCollection();
        $existingCollaborators = new ArrayCollection();
        $existingCollaborators->add($author);

        $issueWithoutCollaborators->method('getCollaborators')
            ->willReturn($emptyCollaborators);

        $issueWithCollaborators->method('getCollaborators')
            ->willReturn($existingCollaborators);

        $issueWithoutCollaborators->expects($this->once())
            ->method('addCollaborator')
            ->with($this->isInstanceOf('TrackerBundle\Entity\User'));

        $issueWithCollaborators->expects($this->never())
            ->method('addCollaborator');

        $commentWithoutCollaborators->method('getIssue')
            ->willReturn($issueWithoutCollaborators);
        $commentWithoutCollaborators->method('getAuthor')
            ->willReturn($author);

        $commentWithCollaborators->method('getIssue')
            ->willReturn($issueWithCollaborators);
        $commentWithCollaborators->method('getAuthor')
            ->willReturn($author);

        $tokenStorage = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage');

        $eventArgs = $this->getMockBuilder('Doctrine\ORM\Event\PreFlushEventArgs')
            ->disableOriginalConstructor()
            ->getMock();

        $commentListener = new CommentListener($tokenStorage);

        $commentListener->preFlush($commentWithoutCollaborators, $eventArgs);
        $commentListener->preFlush($commentWithCollaborators, $eventArgs);
    }

    public function testPostPersist()
    {
        $issue = $this->getMock('TrackerBundle\Entity\Issue');
        $comment = $this->getMock('TrackerBundle\Entity\Comment');
        $comment->method('getIssue')
            ->willReturn($issue);

        $tokenStorage = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage');

        $entityManager = $this
            ->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf('TrackerBundle\Entity\Activity'));
        $entityManager->expects($this->once())
            ->method('flush');

        $eventArgs = $this->getMockBuilder('Doctrine\ORM\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock();
        $eventArgs->method('getEntityManager')
            ->willReturn($entityManager);

        $commentListener = new CommentListener($tokenStorage);

        $commentListener->postPersist($comment, $eventArgs);
    }

    public function testPreUpdate()
    {
        $issue = $this->getMock('TrackerBundle\Entity\Issue');
        $comment = $this->getMock('TrackerBundle\Entity\Comment');
        $comment->method('getIssue')
            ->willReturn($issue);

        $tokenStorage = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage');

        $eventArgs = $this->getMockBuilder('Doctrine\ORM\Event\PreUpdateEventArgs')
            ->disableOriginalConstructor()
            ->getMock();
        $eventArgs->method('hasChangedField')
            ->willReturn('body');

        $commentListener = new CommentListener($tokenStorage);
        $reflector = new \ReflectionClass('TrackerBundle\Entity\Listener\CommentListener');
        $property = $reflector->getProperty('entitiesToFlush');
        $property->setAccessible(true);

        $this->assertTrue($property->getValue($commentListener) === []);
        $commentListener->preUpdate($comment, $eventArgs);
        $this->assertTrue($property->getValue($commentListener)[0] instanceof Activity);
    }
}
