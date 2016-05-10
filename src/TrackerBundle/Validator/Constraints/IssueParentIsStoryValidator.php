<?php

namespace TrackerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use TrackerBundle\Entity\Issue;

class IssueParentIsStoryValidator extends ConstraintValidator
{
    /**
     * @param Issue $issue
     * @param Constraint $constraint
     */
    public function validate($issue, Constraint $constraint)
    {
        if (!empty($issue->getParentIssue()) and $issue->getParentIssue()->getType() != 'TYPE_STORY') {
            $this->context->buildViolation($constraint->message)
                ->atPath('parent_issue')
                ->addViolation();
        }
    }
}
