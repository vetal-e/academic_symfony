<?php

namespace TrackerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TrackerBundle\Entity\Comment;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('body', 'textarea', [
                'label' => 'Text',
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
            'data_class' => 'TrackerBundle\Entity\Comment',
        ));
    }

    public function getName()
    {
        return 'comment';
    }
}