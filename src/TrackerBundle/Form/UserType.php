<?php

namespace TrackerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TrackerBundle\Entity\User;

class UserType extends AbstractType
{
    protected $canChangeRoles;

    public function __construct($canChangeRoles = false)
    {
        $this->canChangeRoles = $canChangeRoles;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email')
            ->add('username', 'text')
            ->add('fullName', 'text', [
                'required' => false,
            ])
            ->add('roles', 'choice', [
                'choices' => [
                    User::ROLE_OPERATOR => 'ROLE_OPERATOR',
                    User::ROLE_MANAGER => 'ROLE_MANAGER',
                    User::ROLE_ADMIN => 'ROLE_ADMIN',
                ],
                'choices_as_values' => true,
                'multiple' => true,
                'disabled' => !$this->canChangeRoles,
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
            'data_class' => 'TrackerBundle\Entity\User',
        ));
    }

    public function getName()
    {
        return 'user';
    }
}