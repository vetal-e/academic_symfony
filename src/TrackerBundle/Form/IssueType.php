<?php

namespace TrackerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TrackerBundle\Entity\Issue;

class IssueType extends AbstractType
{
    protected $action;

    public function __construct($action = 'create')
    {
        $this->action = $action;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('project', 'entity', [
                'class' => 'TrackerBundle\Entity\Project',
                'disabled' => 'true',
            ])
            ->add('summary', 'text')
            ->add('code', 'text', [
                'attr' => [
                    'maxlength' => 20,
                ]
            ]);

        if ($this->action === 'edit') {
            $builder
                ->add('status', 'choice', [
                    'choices' => [
                        Issue::STATUS_OPEN => 'STATUS_OPEN',
                        Issue::STATUS_IN_PROGRESS => 'STATUS_IN_PROGRESS',
                        Issue::STATUS_CLOSED => 'STATUS_CLOSED',
                    ],
                    'choices_as_values' => true,
                ]);
        }

        $builder
            ->add('type', 'choice', [
                'choices' => [
                    Issue::TYPE_BUG => 'TYPE_BUG',
                    Issue::TYPE_TASK => 'TYPE_TASK',
                    Issue::TYPE_SUBTASK => 'TYPE_SUBTASK',
                    Issue::TYPE_STORY => 'TYPE_STORY',
                ],
                'choices_as_values' => true,
            ])
            ->add('priority', 'choice', [
                'choices' => [
                    Issue::PRIORITY_NORMAL => 'PRIORITY_NORMAL',
                    Issue::PRIORITY_HIGH => 'PRIORITY_HIGH',
                    Issue::PRIORITY_LOW => 'PRIORITY_LOW',
                    Issue::PRIORITY_URGENT => 'PRIORITY_URGENT',
                ],
                'choices_as_values' => true,
            ]);

        if ($this->action === 'edit') {
            $builder
                ->add('resolution', 'choice', [
                    'choices' => [
                        Issue::RESOLUTION_RESOLVED => 'RESOLUTION_RESOLVED',
                        Issue::RESOLUTION_REOPENED => 'RESOLUTION_REOPENED',
                    ],
                    'choices_as_values' => true,
                    'required'    => false,
                    'empty_data'  => null,
                ]);
        }

        $builder
            ->add('assignee', 'entity', [
                'class' => 'TrackerBundle\Entity\User',
                'choice_label' => 'username',
                'required'    => false,
                'empty_data'  => null,
                'label' => 'Assign to',
            ])
            ->add('parent_issue', 'entity', [
                'class' => 'TrackerBundle\Entity\Issue',
                'required'    => false,
                'empty_data'  => null,
            ])
            ->add('description', 'textarea', [
                'required' => false,
                'attr' => [
                    'rows' => 5,
                    'class' => 'resize-vertical',
                ]
            ])
            ->add('save', 'submit', [
                'label' => 'Save',
                'attr' => [
                    'class' => 'btn-lg btn-primary pull-right'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TrackerBundle\Entity\Issue',
        ));
    }

    public function getName()
    {
        return 'issue';
    }
}
