<?php

namespace Acme\MainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Service
 *
 * @ORM\Table(name="services")
 * @ORM\Entity(repositoryClass="Acme\MainBundle\Entity\ServiceRepository")
 */
class Service
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Category", mappedBy="services")
     **/
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="Company", mappedBy="servicesArray")
     **/
    private $companies;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->companies = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Service
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add categories
     *
     * @param \Acme\MainBundle\Entity\Category $categories
     * @return Service
     */
    public function addCategory(\Acme\MainBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Acme\MainBundle\Entity\Category $categories
     */
    public function removeCategory(\Acme\MainBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add companies
     *
     * @param \Acme\MainBundle\Entity\Company $companies
     * @return Service
     */
    public function addCompany(\Acme\MainBundle\Entity\Company $companies)
    {
        $this->companies[] = $companies;

        return $this;
    }

    /**
     * Remove companies
     *
     * @param \Acme\MainBundle\Entity\Company $companies
     */
    public function removeCompany(\Acme\MainBundle\Entity\Company $companies)
    {
        $this->companies->removeElement($companies);
    }

    /**
     * Get companies
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCompanies()
    {
        return $this->companies;
    }
}
