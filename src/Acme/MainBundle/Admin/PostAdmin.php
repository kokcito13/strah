<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 6/15/14
 * Time: 10:18 PM
 */

namespace Acme\MainBundle\Admin;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Acme\MainBundle\Entity\Post;

use Knp\Menu\ItemInterface as MenuItemInterface;


class PostAdmin extends Admin
{
    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('title')
            ->add('description')
            ->add('text')
            ->add('tags')
            ->add('category', null, array('label' => 'Идентификатор'));
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        // get the current Image instance
        $image = $this->getSubject();

        // use $fileFieldOptions so we can add other options to the field
        $fileFieldOptions = array('required' => false,'label' => 'Основная картинка');
        if ($image && ($webPath = $image->getWebPath())) {
            // get the container so the full path to the image can be set
            $container = $this->getConfigurationPool()->getContainer();
            $fullPath = $container->get('request')->getBasePath().'/'.$webPath;

            // add a 'help' option containing the preview's img tag
            $fileFieldOptions['help'] = '<img src="'.$fullPath.'" class="admin-preview" />';
        }

        // get the current Image instance
        $imageTop = $this->getSubject();

        // use $fileFieldOptions so we can add other options to the field
        $fileFieldOptionsTop = array('required' => false,'label' => 'Картинка в посте');
        if ($imageTop && ($webPathTop = $imageTop->getWebPathTop())) {
            // get the container so the full path to the image can be set
            $container = $this->getConfigurationPool()->getContainer();
            $fullPathTop = $container->get('request')->getBasePath().'/'.$webPathTop;

            // add a 'help' option containing the preview's img tag
            $fileFieldOptionsTop['help'] = '<img src="'.$fullPathTop.'" class="admin-preview" />';
        }

        $formMapper
            ->with('Главное')
                ->add('file', 'file', $fileFieldOptions)
//                ->add('fileTop', 'file', $fileFieldOptionsTop)
                ->add('imageAlt', 'text', array('label'=>'Описание картинки'))
                ->add('name', 'text', array('label'=>'Название'))
                ->add('url', 'text', array('label'=>'Урл'))
                ->add('title', 'text', array('label'=>'Title'))
                ->add('description', 'textarea', array('label'=>'Description'))
                ->add('keywords', 'textarea', array('label'=>'Keywords', 'required'  => false))
                ->add('shortText', 'textarea', array('label'=>'Короткое описание', 'required'  => false))
                ->add('category', 'entity',
                    array('label' => 'Категория', 'required'  => true, 'class'=>'AcmeMainBundle:Category',
                    'property'=>'name'))
                ->add('text', 'textarea', array('label' => 'Текст', 'attr' => array('class' => 'ckeditor')))
                ->add('video', 'textarea', array('label' => 'Видео (через запятую)', 'required'  => false))
            ->end()
            ->with('Теги')
                ->add('tags', 'sonata_type_model', array('expanded' => true, 'multiple' => true, 'property'=>'name'))
            ->end()
            ->with('Доп. информация', array('collapsed' => true))
                ->add('createdAt')
            ->end()
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
//            ->addIdentifier('url')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ));
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     *
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
//            ->add('enabled')
//            ->add('category', null, array('field_options' => array('expanded' => true, 'property'=>'name')))
//            ->add('tags', null, array('field_options' => array('expanded' => true, 'multiple' => true, 'property'=>'name')))
        ;
    }

    public function prePersist($post)
    {
        $user = $this->getConfigurationPool()->getContainer()->get('security.context')->getToken()->getUser();
        $post->setUser($user);
        $post->setImageFromFile();
        $post->setImageTopFromFile();
    }
    
    public function postPersist($post)
    {
        $this->saveFile($post);
    }
    
    public function preUpdate($post)
    {
        $this->saveFile($post);

        $cacheKey = 'post_pereink_'.$post->getId();
        $this->getConfigurationPool()->getContainer()->get('cache.m')
            ->delete($cacheKey);
    }

    public function saveFile(Post $post)
    {
        $basepath = $this->getRequest()->getBasePath();
        $post->upload($basepath);
        $post->uploadTop($basepath);
    }
}