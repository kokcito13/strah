<?php

namespace Acme\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName')
            ->add('firstName')

            ->add('submit', 'submit', array('label'=>'Сохранить'))
            ->add('reset', 'submit', array('label'=>'Отмена'))
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'acme_userbundle_user';
    }
}
