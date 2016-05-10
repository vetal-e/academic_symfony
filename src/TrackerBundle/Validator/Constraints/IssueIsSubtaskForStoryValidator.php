<?php

namespace TrackerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use TrackerBundle\Entity\Issue;

class IssueIsSubtaskForStoryValidator extends ConstraintValidator
{
    /**
     * @param Issue $issue
     * @param Constraint $constraint
     */
    public function validate($issue, Constraint $constraint)
    {
        if (!empty($issue->getParentIssue()) and $issue->getType() != 'TYPE_SUBTASK') {
            $this->context->buildViolation($constraint->message)
                ->atPath('parent_issue')
                ->addViolation();
        }
    }
}
