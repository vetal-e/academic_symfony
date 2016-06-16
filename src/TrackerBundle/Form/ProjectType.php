<?php

namespace TrackerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', 'text')
            ->add('code', 'text', [
                'attr' => [
                    'maxlength' => 20,
                ]
            ])
            ->add('summary', 'textarea', [
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
            'data_class' => 'TrackerBundle\Entity\Project',
        ));
    }

    public function getName()
    {
        return 'project';
    }
}
