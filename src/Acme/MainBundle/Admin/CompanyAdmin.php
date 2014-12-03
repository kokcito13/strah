<?php

namespace Acme\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CompanyAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('url')
            ->add('title')
            ->add('description')
            ->add('keywords')
            ->add('text')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name')
            ->add('url')
            ->add('title')
            ->add('description')
            ->add('keywords')
            ->add('text')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('url')
            ->add('title')
            ->add('description')
            ->add('keywords')
            ->add('city', 'entity',
                array('label' => 'Город', 'required'  => true, 'class'=>'AcmeMainBundle:City',
                    'property'=>'name'))
            ->add('text', 'textarea', array('label' => 'Текст', 'attr' => array('class' => 'ckeditor')))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('url')
            ->add('title')
            ->add('description')
            ->add('keywords')
            ->add('text')
        ;
    }
}
