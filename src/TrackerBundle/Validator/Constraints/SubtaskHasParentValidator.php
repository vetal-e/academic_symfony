<?php

namespace TrackerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use TrackerBundle\Entity\Issue;

class SubtaskHasParentValidator extends ConstraintValidator
{
    /**
     * @param Issue $issue
     * @param Constraint $constraint
     */
    public function validate($issue, Constraint $constraint)
    {
        if ($issue->getType() === 'TYPE_SUBTASK' and empty($issue->getParentIssue())) {
            $this->context->buildViolation($constraint->message)
                ->atPath('parent_issue')
                ->addViolation();
        }
    }
}
