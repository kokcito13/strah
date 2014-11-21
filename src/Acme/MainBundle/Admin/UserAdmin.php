<?php

namespace Acme\MainBundle\Admin;

use Sonata\UserBundle\Admin\Model\UserAdmin as SonataUserAdmin;

use Application\Sonata\UserBundle\Entity\User as User;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class UserAdmin extends SonataUserAdmin
{

   /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $image = $this->getSubject();
        $fileFieldOptions = array('required' => false,'label' => 'Аватар');
        if ($image && ($webPath = $image->getWebPath())) {
            $container = $this->getConfigurationPool()->getContainer();
            $fullPath = $container->get('request')->getBasePath().'/'.$webPath;
            $fileFieldOptions['help'] = '<img src="'.$fullPath.'" class="admin-preview" />';
        }

        parent::configureFormFields($formMapper);

        $formMapper
            ->with('Profile')
            ->add('file', 'file', $fileFieldOptions)
            ->end()
        ;
    }


    public function prePersist($user)
    {
        $user->setImageFromFile();
    }

    public function postPersist($user)
    {
        $this->saveFile($user);
    }

    public function preUpdate($user)
    {
        $this->saveFile($user);
    }

    public function saveFile(User $user)
    {
        $basepath = $this->getRequest()->getBasePath();
        $user->upload($basepath);
    }

}