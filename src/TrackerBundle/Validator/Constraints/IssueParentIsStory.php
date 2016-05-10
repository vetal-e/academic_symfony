<?php

namespace TrackerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IssueParentIsStory extends Constraint
{
    public $message = 'Only a story can be set as subtask\'s parent';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
