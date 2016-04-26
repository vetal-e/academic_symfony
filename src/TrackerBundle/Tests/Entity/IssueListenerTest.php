<?php

namespace TrackerBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use TrackerBundle\Entity\Activity;
use TrackerBundle\Entity\Listener\IssueListener;

class IssueListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testPreFlush()
    {
        $issueWithoutCollaborators = $this->getMock('TrackerBundle\Entity\Issue');
        $issueWithCollaborators = $this->getMock('TrackerBundle\Entity\Issue');

        $reporter = $this->getMock('TrackerBundle\Entity\User');
        $assignee = $this->getMock('TrackerBundle\Entity\User');

        $emptyCollaborators = new ArrayCollection();
        $existingCollaborators = new ArrayCollection();
        $existingCollaborators->add($reporter);
        $existingCollaborators->add($assignee);

        $issueWithoutCollaborators->method('getReporter')
            ->willReturn($reporter);
        $issueWithoutCollaborators->method('getAssignee')
            ->willReturn($assignee);
        $issueWithoutCollaborators->method('getCollaborators')
            ->willReturn($emptyCollaborators);

        $issueWithCollaborators->method('getReporter')
            ->willReturn($reporter);
        $issueWithCollaborators->method('getAssignee')
            ->willReturn($assignee);
        $issueWithCollaborators->method('getCollaborators')
            ->willReturn($existingCollaborators);

        $issueWithoutCollaborators->expects($this->exactly(2))
            ->method('addCollaborator')
            ->with($this->isInstanceOf('TrackerBundle\Entity\User'));

        $issueWithCollaborators->expects($this->never())
            ->method('addCollaborator');

        $tokenStorage = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage');

        $eventArgs = $this->getMockBuilder('Doctrine\ORM\Event\PreFlushEventArgs')
            ->disableOriginalConstructor()
            ->getMock();

        $issueListener = new IssueListener($tokenStorage);

        $issueListener->preFlush($issueWithoutCollaborators, $eventArgs);
        $issueListener->preFlush($issueWithCollaborators, $eventArgs);
    }

    public function testPostPersist()
    {
        $issue = $this->getMock('TrackerBundle\Entity\Issue');

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

        $issueListener = new IssueListener($tokenStorage);

        $issueListener->postPersist($issue, $eventArgs);
    }

    public function testPreUpdate()
    {
        $issue = $this->getMock('TrackerBundle\Entity\Issue');

        $tokenStorage = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage');

        $eventArgs = $this->getMockBuilder('Doctrine\ORM\Event\PreUpdateEventArgs')
            ->disableOriginalConstructor()
            ->getMock();
        $eventArgs->method('hasChangedField')
            ->willReturn('status');

        $issueListener = new IssueListener($tokenStorage);
        $reflector = new \ReflectionClass('TrackerBundle\Entity\Listener\IssueListener');
        $property = $reflector->getProperty('entitiesToFlush');
        $property->setAccessible(true);

        $this->assertTrue($property->getValue($issueListener) === []);
        $issueListener->preUpdate($issue, $eventArgs);
        $this->assertTrue($property->getValue($issueListener)[0] instanceof Activity);
    }
}
