<?php

namespace TrackerBundle\Tests\Security\Authorization\Voter;

use Doctrine\Common\Collections\ArrayCollection;
use TrackerBundle\Security\Authorization\Voter\ProjectVoter;

class ProjectVoterTest extends \PHPUnit_Framework_TestCase
{
    public function projectProvider()
    {
        return [
            ['view', 'ROLE_OPERATOR', true, true],
            ['view', 'ROLE_MANAGER',  true, true],
            ['view', 'ROLE_ADMIN',    true, true],

            ['create', 'ROLE_OPERATOR', true, false],
            ['create', 'ROLE_MANAGER',  true, true],
            ['create', 'ROLE_ADMIN',    true, true],

            ['edit', 'ROLE_OPERATOR', true, false],
            ['edit', 'ROLE_MANAGER',  true, true],
            ['edit', 'ROLE_ADMIN',    true, true],

            ['create_issue', 'ROLE_OPERATOR', true, true],
            ['create_issue', 'ROLE_MANAGER',  true, true],
            ['create_issue', 'ROLE_ADMIN',    true, true],

            ['manage_members', 'ROLE_OPERATOR', true, false],
            ['manage_members', 'ROLE_MANAGER',  true, true],
            ['manage_members', 'ROLE_ADMIN',    true, true],

            ['view', 'ROLE_OPERATOR', false, false],
            ['view', 'ROLE_MANAGER',  false, true],
            ['view', 'ROLE_ADMIN',    false, true],

            ['create', 'ROLE_OPERATOR', false, false],
            ['create', 'ROLE_MANAGER',  false, true],
            ['create', 'ROLE_ADMIN',    false, true],

            ['edit', 'ROLE_OPERATOR', false, false],
            ['edit', 'ROLE_MANAGER',  false, true],
            ['edit', 'ROLE_ADMIN',    false, true],

            ['create_issue', 'ROLE_OPERATOR', false, false],
            ['create_issue', 'ROLE_MANAGER',  false, true],
            ['create_issue', 'ROLE_ADMIN',    false, true],

            ['manage_members', 'ROLE_OPERATOR', false, false],
            ['manage_members', 'ROLE_MANAGER',  false, true],
            ['manage_members', 'ROLE_ADMIN',    false, true],
        ];
    }

    /**
     * @dataProvider projectProvider
     */
    public function testIsGranted($action, $role, $isMember, $expected)
    {
        $failedAssertMessage = 'by non project member';

        $voter = new ProjectVoter();
        $reflector = new \ReflectionClass('TrackerBundle\Security\Authorization\Voter\ProjectVoter');
        $method = $reflector->getMethod('isGranted');
        $method->setAccessible(true);

        $user = $this->getMock('TrackerBundle\Entity\User');
        $user->expects($this->atLeastOnce())
            ->method('getRoles')
            ->will($this->returnValue([$role]))
        ;

        $projectToEdit = $this->getMock('TrackerBundle\Entity\Project');
        $members = new ArrayCollection();

        if ($isMember) {
            $members->add($user);
            $failedAssertMessage = 'by project member';
        }

        $projectToEdit
            ->method('getMembers')
            ->willReturn($members)
        ;

        $result = $method->invokeArgs($voter, [$action, $projectToEdit, $user]);
        $this->assertEquals($expected, $result, "$role $action $failedAssertMessage");
    }
}
