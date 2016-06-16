<?php

namespace TrackerBundle\Tests\Security\Authorization\Voter;

use TrackerBundle\Security\Authorization\Voter\UserVoter;

class UserVoterTest extends \PHPUnit_Framework_TestCase
{
    public function userProvider()
    {
        return [
            ['edit', 'ROLE_OPERATOR', false, false],
            ['edit', 'ROLE_MANAGER',  false, false],
            ['edit', 'ROLE_ADMIN',    false, true],

            ['change_roles', 'ROLE_OPERATOR', false, false],
            ['change_roles', 'ROLE_MANAGER',  false, false],
            ['change_roles', 'ROLE_ADMIN',    false, true],

            ['create_project', 'ROLE_OPERATOR', false, false],
            ['create_project', 'ROLE_MANAGER',  false, true],
            ['create_project', 'ROLE_ADMIN',    false, true],

            ['edit', 'ROLE_OPERATOR', true, true],
            ['edit', 'ROLE_MANAGER',  true, true],
            ['edit', 'ROLE_ADMIN',    true, true],

            ['change_roles', 'ROLE_OPERATOR', true, false],
            ['change_roles', 'ROLE_MANAGER',  true, false],
            ['change_roles', 'ROLE_ADMIN',    true, true],

            ['create_project', 'ROLE_OPERATOR', true, false],
            ['create_project', 'ROLE_MANAGER',  true, true],
            ['create_project', 'ROLE_ADMIN',    true, true],
        ];
    }

    /**
     * @dataProvider userProvider
     */
    public function testIsGranted($action, $role, $bySameUser, $expected)
    {
        $voter = new UserVoter();
        $reflector = new \ReflectionClass('TrackerBundle\Security\Authorization\Voter\UserVoter');
        $method = $reflector->getMethod('isGranted');
        $method->setAccessible(true);

        $user = $this->getMock('TrackerBundle\Entity\User');
        $user->expects($this->atLeastOnce())
            ->method('getRoles')
            ->will($this->returnValue([$role]))
        ;

        if ($bySameUser) {
            $userToEdit = $user;
            $failedAssertMessage = 'by the same user';
        } else {
            $userToEdit = $this->getMock('TrackerBundle\Entity\User');
            $failedAssertMessage = 'by different user';
        }

        $result = $method->invokeArgs($voter, [$action, $userToEdit, $user]);
        $this->assertEquals($expected, $result, "$role $action $failedAssertMessage");
    }
}
