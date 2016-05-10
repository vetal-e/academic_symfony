<?php

namespace TrackerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SubtaskHasParent extends Constraint
{
    public $message = 'Subtask should have a story as a parent';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
