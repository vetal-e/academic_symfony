<?php

namespace TrackerBundle\Tests\Security\Authorization\Voter;

use Doctrine\Common\Collections\ArrayCollection;
use TrackerBundle\Security\Authorization\Voter\IssueVoter;

class IssueVoterTest extends \PHPUnit_Framework_TestCase
{
    public function issueProvider()
    {
        return [
            ['view', 'ROLE_OPERATOR', false, false],
            ['view', 'ROLE_MANAGER',  false, true],
            ['view', 'ROLE_ADMIN',    false, true],

            ['create', 'ROLE_OPERATOR', false, false],
            ['create', 'ROLE_MANAGER',  false, true],
            ['create', 'ROLE_ADMIN',    false, true],

            ['edit', 'ROLE_OPERATOR', false, false],
            ['edit', 'ROLE_MANAGER',  false, true],
            ['edit', 'ROLE_ADMIN',    false, true],

            ['comment', 'ROLE_OPERATOR', false, false],
            ['comment', 'ROLE_MANAGER',  false, true],
            ['comment', 'ROLE_ADMIN',    false, true],

            ['view', 'ROLE_OPERATOR', true, true],
            ['view', 'ROLE_MANAGER',  true, true],
            ['view', 'ROLE_ADMIN',    true, true],

            ['create', 'ROLE_OPERATOR', true, true],
            ['create', 'ROLE_MANAGER',  true, true],
            ['create', 'ROLE_ADMIN',    true, true],

            ['edit', 'ROLE_OPERATOR', true, true],
            ['edit', 'ROLE_MANAGER',  true, true],
            ['edit', 'ROLE_ADMIN',    true, true],

            ['comment', 'ROLE_OPERATOR', true, true],
            ['comment', 'ROLE_MANAGER',  true, true],
            ['comment', 'ROLE_ADMIN',    true, true],
        ];
    }

    /**
     * @dataProvider issueProvider
     */
    public function testIsGranted($action, $role, $isMember, $expected)
    {
        $voter = new IssueVoter();
        $reflector = new \ReflectionClass('TrackerBundle\Security\Authorization\Voter\IssueVoter');
        $method = $reflector->getMethod('isGranted');
        $method->setAccessible(true);

        $user = $this->getMock('TrackerBundle\Entity\User');
        $user->expects($this->atLeastOnce())
            ->method('getRoles')
            ->will($this->returnValue([$role]))
        ;

        $project = $this->getMock('TrackerBundle\Entity\Project');
        $members = new ArrayCollection();

        $failedAssertMessage = 'by non project member';
        if ($isMember) {
            $members->add($user);
            $failedAssertMessage = 'by project member';
        }

        $project
            ->method('getMembers')
            ->willReturn($members)
        ;

        $issue = $this->getMock('TrackerBundle\Entity\Issue');
        $issue
            ->method('getProject')
            ->willReturn($project)
        ;

        $result = $method->invokeArgs($voter, [$action, $issue, $user]);
        $this->assertEquals($expected, $result, "$role $action $failedAssertMessage");
    }
}