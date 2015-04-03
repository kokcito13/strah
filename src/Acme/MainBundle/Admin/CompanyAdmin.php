<?php

namespace Acme\MainBundle\Admin;

use Acme\MainBundle\Entity\Company;
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
//            ->add('description')
//            ->add('keywords')
//            ->add('text')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name')
//            ->add('url')
//            ->add('title')
//            ->add('description')
//            ->add('keywords')
//            ->add('text')
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
        // get the current Image instance
        $image = $this->getSubject();

        // use $fileFieldOptions so we can add other options to the field
        $fileFieldOptions = array('required' => false,'label' => 'Логотип');
        if ($image && ($webPath = $image->getWebPath())) {
            // get the container so the full path to the image can be set
            $container = $this->getConfigurationPool()->getContainer();
            $fullPath = $container->get('request')->getBasePath().'/'.$webPath;

            // add a 'help' option containing the preview's img tag
            $fileFieldOptions['help'] = '<img src="'.$fullPath.'" class="admin-preview" />';
        }

        $formMapper
            ->with('Главное')
                ->add('name', 'text', array('label' => 'Название'))
                ->add('file', 'file', $fileFieldOptions)
                ->add('imageAlt', 'text', array('label' => 'Описание картинки'))
                ->add('site', 'text', array('label' => 'Сайт'))
                ->add('url')
                ->add('city', 'entity',
                    array('label' => 'Город', 'required'  => true, 'class'=>'AcmeMainBundle:City',
                        'property'=>'name'))
                ->add('services', 'textarea', array('label' => 'Виды услуг', 'attr' => array('class' => 'ckeditor')))
                ->add('contacts', 'textarea', array('label' => 'Контакты', 'attr' => array('class' => 'ckeditor')))
                ->add('text', 'textarea', array('label' => 'Инфо', 'attr' => array('class' => 'ckeditor')))
                ->add('rating', 'integer', array('label' => 'Рейтинг'))
            ->end()
            ->with('СЕО')
                ->add('title')
                ->add('description')
                ->add('keywords')
            ->end()
            ->with('Услуги')
                ->add('servicesArray', 'sonata_type_model', array('expanded' => true, 'multiple' => true, 'property'=>'name'))
            ->end()
            ->with('Point')
            ->add('points', 'sonata_type_collection',
                array('label' => 'Point', 'by_reference' => false),
                array(
                    'edit' => 'inline',
                    //В сущности NewsLink есть поле pos, отражающее положение ссылки в списке
                    //указание опции sortable позволяет менять положение ссылок в списке перетаскиваением
                    'inline' => 'table',
                ))
            ->end()
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

    public function prePersist($company)
    {
        $company->setImageFromFile();


    }

    public function postPersist($company)
    {
        $this->saveFile($company);
    }

    public function preUpdate($company)
    {
        $this->saveFile($company);

        foreach ($company->getPoints() as $point) {
            $point->setCompany($company);
        }
    }

    public function saveFile(Company $company)
    {
        $basepath = $this->getRequest()->getBasePath();
        $company->upload($basepath);
    }
}
