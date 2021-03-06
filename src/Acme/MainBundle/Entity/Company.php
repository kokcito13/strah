<?php

namespace Acme\MainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Company
 *
 * @ORM\Table(name="company")
 * @ORM\Entity(repositoryClass="Acme\MainBundle\Entity\CompanyRepository")
 */
class Company
{
    const STATUS_ON = true;
    const STATUS_OFF = false;

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
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="contacts", type="text", nullable=true)
     */
    private $contacts;

    /**
     * @var string
     *
     * @ORM\Column(name="services", type="text", nullable=true)
     */
    private $services;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="site", type="string", length=255, nullable=true)
     */
    private $site;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="string", length=255)
     */
    private $keywords;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     */
    private $text;


    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="companies")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="image_name", type="string", length=255, nullable=true)
     */
    protected $imageName;

    protected $file;

    /**
     * @var integer
     *
     * @ORM\Column(name="rating", type="integer")
     */
    private $rating;

    /**
     * @var string
     *
     * @ORM\Column(name="image_alt", type="string", length=255, nullable=true)
     */
    private $imageAlt;

    /**
     * @ORM\ManyToMany(targetEntity="Service", inversedBy="companies")
     * @ORM\JoinTable(name="company_services")
     **/
    private $servicesArray;

    /**
     * @ORM\OneToMany(targetEntity="CompanyPoint", mappedBy="company", cascade={"persist"}, orphanRemoval=true)
     **/
    private $points;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="company")
     **/
    private $comments;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    public function __construct()
    {
        $this->rating = 0;
        $this->servicesArray = new ArrayCollection();
        $this->points = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->status = self::STATUS_ON;
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
     * @return Company
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
     * Set url
     *
     * @param string $url
     * @return Company
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Company
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Company
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return Company
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Company
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set city
     *
     * @param \Acme\MainBundle\Entity\City $city
     * @return Company
     */
    public function setCity(\Acme\MainBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \Acme\MainBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }


    public function getAbsolutePath()
    {
        return null === $this->imageName ? null : $this->getUploadRootDir('') . '/' . $this->imageName;
    }

    public function getWebPath()
    {
        return null === $this->imageName ? null : $this->getUploadDir() . '/' . $this->imageName;
    }

    protected function getUploadRootDir($basepath)
    {
        return $basepath . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/company/'.$this->getId();
    }

    public function upload($basepath)
    {
        if (null === $this->file) {
            return;
        }

        if (null === $basepath) {
            return;
        }

        $this->file->move($this->getUploadRootDir($basepath), $this->file->getClientOriginalName());
        $this->setImageName($this->file->getClientOriginalName());
        $this->file = null;
    }

    public function setImageFromFile()
    {
        if (null === $this->file) {
            return;
        }
        $this->setImageName($this->file->getClientOriginalName());
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    public function setImageName($image_name)
    {
        $this->imageName = $image_name;

        return $this;
    }

    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Company
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set contacts
     *
     * @param string $contacts
     * @return Company
     */
    public function setContacts($contacts)
    {
        $this->contacts = $contacts;

        return $this;
    }

    /**
     * Get contacts
     *
     * @return string 
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Set services
     *
     * @param string $services
     * @return Company
     */
    public function setServices($services)
    {
        $this->services = $services;

        return $this;
    }

    /**
     * Get services
     *
     * @return string 
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Set site
     *
     * @param string $site
     * @return Company
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return string 
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     * @return Company
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return integer 
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set imageAlt
     *
     * @param string $imageAlt
     * @return Company
     */
    public function setImageAlt($imageAlt)
    {
        $this->imageAlt = $imageAlt;

        return $this;
    }

    /**
     * Get imageAlt
     *
     * @return string 
     */
    public function getImageAlt()
    {
        return $this->imageAlt;
    }

    public function getImageAltView()
    {
        if (!is_null($this->getImageAlt()))
            return $this->imageAlt;

        return 'Логотип компании - '.$this->getName();
    }

    /**
     * Add servicesArray
     *
     * @param \Acme\MainBundle\Entity\Service $servicesArray
     * @return Company
     */
    public function addServicesArray(\Acme\MainBundle\Entity\Service $servicesArray)
    {
        $this->servicesArray[] = $servicesArray;

        return $this;
    }

    /**
     * Remove servicesArray
     *
     * @param \Acme\MainBundle\Entity\Service $servicesArray
     */
    public function removeServicesArray(\Acme\MainBundle\Entity\Service $servicesArray)
    {
        $this->servicesArray->removeElement($servicesArray);
    }

    /**
     * Get servicesArray
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getServicesArray()
    {
        return $this->servicesArray;
    }

    /**
     * Add points
     *
     * @param \Acme\MainBundle\Entity\CompanyPoint $points
     * @return Company
     */
    public function addPoint(\Acme\MainBundle\Entity\CompanyPoint $points)
    {
        $this->points[] = $points;

        return $this;
    }

    /**
     * Remove points
     *
     * @param \Acme\MainBundle\Entity\CompanyPoint $points
     */
    public function removePoint(\Acme\MainBundle\Entity\CompanyPoint $points)
    {
        $this->points->removeElement($points);
    }

    /**
     * Get points
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Add comments
     *
     * @param \Acme\MainBundle\Entity\Comment $comments
     * @return Company
     */
    public function addComment(\Acme\MainBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Acme\MainBundle\Entity\Comment $comments
     */
    public function removeComment(\Acme\MainBundle\Entity\Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Company
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function  getNameWithCity()
    {
        return $this->getName().' в '.$this->getCity()->getDative();
    }
}
