<?php

namespace TrackerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IssueIsSubtaskForStory extends Constraint
{
    public $message = 'Only subtasks can have a story as a parent';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
