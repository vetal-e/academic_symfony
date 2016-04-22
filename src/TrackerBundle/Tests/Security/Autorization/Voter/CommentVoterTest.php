<?php

namespace TrackerBundle\Tests\Security\Authorization\Voter;

use Doctrine\Common\Collections\ArrayCollection;
use TrackerBundle\Security\Authorization\Voter\CommentVoter;

class CommentVoterTest extends \PHPUnit_Framework_TestCase
{
    public function commentProvider()
    {
        return [
            ['create', 'ROLE_OPERATOR', false, false, false],
            ['create', 'ROLE_MANAGER',  false, false, false],
            ['create', 'ROLE_ADMIN',    false, false, false],

            ['edit', 'ROLE_OPERATOR', false, false, false],
            ['edit', 'ROLE_MANAGER',  false, false, false],
            ['edit', 'ROLE_ADMIN',    false, false, true],

            ['delete', 'ROLE_OPERATOR', false, false, false],
            ['delete', 'ROLE_MANAGER',  false, false, false],
            ['delete', 'ROLE_ADMIN',    false, false, true],

            ['create', 'ROLE_OPERATOR', true, false, false],
            ['create', 'ROLE_MANAGER',  true, false, false],
            ['create', 'ROLE_ADMIN',    true, false, false],

            ['edit', 'ROLE_OPERATOR', true, false, true],
            ['edit', 'ROLE_MANAGER',  true, false, true],
            ['edit', 'ROLE_ADMIN',    true, false, true],

            ['delete', 'ROLE_OPERATOR', true, false, true],
            ['delete', 'ROLE_MANAGER',  true, false, true],
            ['delete', 'ROLE_ADMIN',    true, false, true],

            ['create', 'ROLE_OPERATOR', false, true, true],
            ['create', 'ROLE_MANAGER',  false, true, true],
            ['create', 'ROLE_ADMIN',    false, true, true],

            ['edit', 'ROLE_OPERATOR', false, true, false],
            ['edit', 'ROLE_MANAGER',  false, true, false],
            ['edit', 'ROLE_ADMIN',    false, true, true],

            ['delete', 'ROLE_OPERATOR', false, true, false],
            ['delete', 'ROLE_MANAGER',  false, true, false],
            ['delete', 'ROLE_ADMIN',    false, true, true],

            ['create', 'ROLE_OPERATOR', true, true, true],
            ['create', 'ROLE_MANAGER',  true, true, true],
            ['create', 'ROLE_ADMIN',    true, true, true],

            ['edit', 'ROLE_OPERATOR', true, true, true],
            ['edit', 'ROLE_MANAGER',  true, true, true],
            ['edit', 'ROLE_ADMIN',    true, true, true],

            ['delete', 'ROLE_OPERATOR', true, true, true],
            ['delete', 'ROLE_MANAGER',  true, true, true],
            ['delete', 'ROLE_ADMIN',    true, true, true],
        ];
    }

    /**
     * @dataProvider commentProvider
     */
    public function testIsGranted($action, $role, $isAuthor, $isProjectMember, $expected)
    {
        $voter = new CommentVoter();
        $reflector = new \ReflectionClass('TrackerBundle\Security\Authorization\Voter\CommentVoter');
        $method = $reflector->getMethod('isGranted');
        $method->setAccessible(true);

        $user = $this->getMock('TrackerBundle\Entity\User');
        $user
            ->method('getRoles')
            ->will($this->returnValue([$role]));

        $project = $this->getMock('TrackerBundle\Entity\Project');
        $members = new ArrayCollection();

        $failedAssertMemberMessage = 'by non project member';
        if ($isProjectMember) {
            $members->add($user);
            $failedAssertMemberMessage = 'by project member';
        }

        $project
            ->method('getMembers')
            ->willReturn($members);

        $issue = $this->getMock('TrackerBundle\Entity\Issue');
        $issue
            ->method('getProject')
            ->willReturn($project);

        $comment = $this->getMock('TrackerBundle\Entity\Comment');
        $comment
            ->method('getIssue')
            ->willReturn($issue);

        if ($isAuthor) {
            $commentAuthor = $user;
            $failedAssertAuthorMessage = 'by comment author';
        } else {
            $commentAuthor = $this->getMock('TrackerBundle\Entity\User');
            $failedAssertAuthorMessage = 'by non comment author';
        }
        $comment
            ->method('getAuthor')
            ->willReturn($commentAuthor);

        $result = $method->invokeArgs($voter, [$action, $comment, $user]);
        $this->assertEquals($expected, $result, "$role $action $failedAssertAuthorMessage $failedAssertMemberMessage");
    }
}