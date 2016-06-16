<?php

namespace TrackerBundle\Tests\Validator\Constraints;

use TrackerBundle\Validator\Constraints\IssueIsSubtaskForStory;
use TrackerBundle\Validator\Constraints\IssueIsSubtaskForStoryValidator;

class IssueIsSubtaskForStoryValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IssueIsSubtaskForStoryValidator
     */
    private $validator;

    /**
     * @var IssueIsSubtaskForStory
     */
    private $constraint;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $builder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $issue;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $parentIssue;

    public function setUp()
    {
        $this->validator = new IssueIsSubtaskForStoryValidator();
        $this->constraint = new IssueIsSubtaskForStory();

        $this->issue = $this->getMock('TrackerBundle\Entity\Issue');
        $this->parentIssue = $this->getMock('TrackerBundle\Entity\Issue');

        $this->builder = $this->getMockBuilder('Symfony\Component\Validator\Violation\ConstraintViolationBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('addViolation'))
            ->getMock();

        $context = $this->getMockBuilder('Symfony\Component\Validator\Context\ExecutionContext')
            ->disableOriginalConstructor()
            ->setMethods(array('buildViolation'))
            ->getMock();

        $context
            ->method('buildViolation')
            ->willReturn($this->builder);

        $this->validator->initialize($context);
    }

    public function tearDown()
    {
        unset($this->validator);
        unset($this->constraint);
        unset($this->builder);
        unset($this->issue);
        unset($this->parentIssue);
    }

    /**
     * Only subtasks can have a parent
     */
    public function commentProvider()
    {
        return [
            [false, false, 0],
            [true,  false, 1],
            [false, true,  0],
            [true,  true,  0],
        ];
    }

    /**
     * @dataProvider commentProvider
     */
    public function testValidate($hasParent, $isSubtask, $expected)
    {
        if ($hasParent) {
            $this->issue
                ->method('getParentIssue')
                ->willReturn($this->parentIssue);
        }

        if ($isSubtask) {
            $this->issue
                ->method('getType')
                ->willReturn('TYPE_SUBTASK');
        }

        $this->builder->expects($this->exactly($expected))
            ->method('addViolation');

        $this->validator->validate($this->issue, $this->constraint);
    }
}
