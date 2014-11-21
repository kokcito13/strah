<?php

namespace Acme\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

class ProfileFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder->add('file', 'avatar_name', array('label'=>'Avatar'));
        //Add all your properties here with $builder->add('property name')
    }

    public function getAvatar()
    {
        return 'fos_user_profile';
    }
}