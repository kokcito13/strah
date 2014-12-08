<?php

namespace Acme\MainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;
use Application\Sonata\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="Acme\MainBundle\Entity\PostRepository")
 * @UniqueEntity("url")
 */
class Post
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
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

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
     * @ORM\Column(name="keywords", type="string", length=255, nullable=true)
     */
    private $keywords;

    /**
     * @var string
     *
     * @ORM\Column(name="short_text", type="text", nullable=true)
     */
    private $shortText;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="video", type="text", nullable=true)
     */
    private $video;

    /**
     * @var integer
     *
     * @ORM\Column(name="view", type="integer")
     */
    private $view;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;


    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="posts")
     * @ORM\JoinTable(name="post_tag")
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="posts")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="image_name", type="string", length=255, nullable=true)
     */
    protected $imageName;

    protected $file;

    /**
     * @var string
     *
     * @ORM\Column(name="image_name_top", type="string", length=255, nullable=true)
     */
    protected $imageNameTop;

    protected $fileTop;

    /**
     * @var string
     *
     * @ORM\Column(name="image_palette", type="string", length=100, nullable=true)
     */
    protected $imagePalette;

    /**
     * @var integer
     *
     * @ORM\Column(name="image_width", type="integer", nullable=true)
     */
    private $imageWidth;

    /**
     * @var integer
     *
     * @ORM\Column(name="image_height", type="integer", nullable=true)
     */
    private $imageHeight;

    /**
     * @var integer
     *
     * @ORM\Column(name="shared_vk", type="integer", nullable=true)
     */
    private $sharedVk;

    /**
     * @var integer
     *
     * @ORM\Column(name="shared_fb", type="integer", nullable=true)
     */
    private $sharedFb;

    /**
     * @var integer
     *
     * @ORM\Column(name="shared_tw", type="integer", nullable=true)
     */
    private $sharedTw;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="image_alt", type="string", length=255, nullable=true)
     */
    protected $imageAlt;

    /**
     * Set user_id
     *
     * @return Post
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user_id
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    private $imagesThumbnailType = array(
        'thumbnail1_category_post' => array(
            'w' => 300,
            'h' => 263,
            'r' => 1.14
        ),
        'thumbnail2_category_post' => array(
            'w' => 300,
            'h' => 153,
            'r' => 1.97
        ),
        'thumbnail3_category_post' => array(
            'w' => 630,
            'h' => 343,
            'r' => 1.83
        )
    );

    private $imageThumbnail = array();

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->createdAt = new \DateTime('now');
        $this->view = 0;
        $this->imageWidth = 0;
        $this->imageHeight = 0;
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
     * @return Post
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
     * @return Post
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
     * @return Post
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
     * @return Post
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
     * Set shortText
     *
     * @param string $shortText
     * @return Post
     */
    public function setShortText($shortText)
    {
        $this->shortText = $shortText;

        return $this;
    }

    /**
     * Get shortText
     *
     * @return string 
     */
    public function getShortText()
    {
        return $this->shortText;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Post
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
     * Set view
     *
     * @param integer $view
     * @return Post
     */
    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get view
     *
     * @return integer 
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Post
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function addTag(Tag $tag)
    {
        $tag->addPost($this);
        $this->tags[] = $tag;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return $this
     */
    public function setCategory(Category $category )
    {
        $this->category = $category;

        return $this;
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
        return 'uploads/posts/'.$this->getId();
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

        $this->setImageParameters();
    }

    public function setImageFromFile()
    {
        if (null === $this->file) {
            return;
        }
        $this->setImageName($this->file->getClientOriginalName());
    }
    public function setImageTopFromFile()
    {
        if (null === $this->fileTop) {
            return;
        }
       $this->setImageNameTop($this->fileTop->getClientOriginalName());
    }
    
    public function setImageParameters()
    {
        $imageFile = $this->getWebPath();
        $granularity = max(1, abs((int)5));
        $colors = array();
        $size = @getimagesize($imageFile);
        if ($size !== false) {
            $this->setImageHeight($size[1]);
            $this->setImageWidth($size[0]);

            $img = @imagecreatefromstring(file_get_contents($imageFile));
            if ($img) {
                for ($x = 0; $x < $size[0]; $x += $granularity) {
                    for ($y = 0; $y < $size[1]; $y += $granularity) {
                        $thisColor = imagecolorat($img, $x, $y);
                        $rgb = imagecolorsforindex($img, $thisColor);
                        $red = round(round(($rgb['red'] / 0x33)) * 0x33);
                        $green = round(round(($rgb['green'] / 0x33)) * 0x33);
                        $blue = round(round(($rgb['blue'] / 0x33)) * 0x33);
                        $thisRGB = $red . ', ' . $green . ', ' . $blue; //sprintf('%02X%02X%02X', $red, $green, $blue);
                        if (array_key_exists($thisRGB, $colors)) {
                            $colors[$thisRGB]++;
                        } else {
                            $colors[$thisRGB] = 1;
                        }
                    }
                }
                arsort($colors);
                $result = array_slice(array_keys($colors), 0, 1);
                $this->setImagePalette(current($result));
            }
        }

        return $this;
    }

    public function getImagePalette()
    {
        return $this->imagePalette;
    }

    public function setImagePalette($palette)
    {
        $this->imagePalette = $palette;

        return $this;
    }

    public function getImageWidth()
    {
        return $this->imageWidth;
    }

    public function setImageWidth($int)
    {
        $this->imageWidth = $int;

        return $this;
    }

    public function getImageHeight()
    {
        return $this->imageHeight;
    }

    public function setImageHeight($int)
    {
        $this->imageHeight = $int;

        return $this;
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

    public function getImageThumbnail()
    {
        if (empty($this->imageThumbnail)) {
            $thumbnailK = 'thumbnail1_category_post';
            $thumbnailP = $this->imagesThumbnailType[$thumbnailK];
            if ($this->getImageWidth() && $this->getImageHeight()) {
                $r = $this->getImageWidth()/$this->getImageHeight();
                foreach ($this->imagesThumbnailType as $k => $v) {
                    $res = abs($r-$v['r']) < abs($r-$thumbnailP['r']);
                    if ($res){
                        $thumbnailP = $v;
                        $thumbnailK = $k;
                    }
                }
            }
            $this->imageThumbnail = array('key' => $thumbnailK, 'par' => $thumbnailP);
        }

        return $this->imageThumbnail;
    }
    
    public function oneMoreView()
    {
        $this->view++;

        return $this;
    }

    public function getFormatCreatedAt()
    {
        return $this->getCreatedAtByFormat('d F Y, H:i');
    }

    public function getCreatedAtByFormat($format)
    {
        $ru_month = array( 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря' );
        $en_month = array( 'January', 'February', 'March', 'May', 'June', 'July', 'August', 'September', 'Oktober', 'November', 'December' );

        $dateTime = $this->getCreatedAt()->getTimestamp();

        return str_replace( $en_month, $ru_month, date($format,$dateTime) );
    }

    public function getImageNameTop()
    {
        return $this->imageNameTop;
    }

    public function setImageNameTop($image)
    {
        $this->imageNameTop = $image;

        return false;
    }

    public function getAbsolutePathTop()
    {
        return null === $this->imageNameTop ? null : $this->getUploadRootDirTop('') . '/' . $this->imageNameTop;
    }

    public function getWebPathTop()
    {
        return null === $this->imageNameTop ? null : $this->getUploadDir() . '/' . $this->imageNameTop;
    }

    protected function getUploadRootDirTop($basepath)
    {
        return $basepath . $this->getUploadDir();
    }

    public function uploadTop($basepath)
    {
        if (null === $this->fileTop)
            return;

        if (null === $basepath)
            return;

        $this->fileTop->move($this->getUploadRootDirTop($basepath), $this->fileTop->getClientOriginalName());
        $this->setImageNameTop($this->fileTop->getClientOriginalName());
        $this->fileTop = null;
    }

    public function getFileTop()
    {
        return $this->fileTop;
    }

    public function setFileTop($file)
    {
        $this->fileTop = $file;

        return $this;
    }

    /**
     * Set sharedVk
     *
     * @param integer $sharedVk
     * @return Post
     */
    public function setSharedVk($sharedVk)
    {
        $this->sharedVk = $sharedVk;

        return $this;
    }

    /**
     * Get sharedVk
     *
     * @return integer 
     */
    public function getSharedVk()
    {
        return (int)$this->sharedVk;
    }

    /**
     * Set sharedFb
     *
     * @param integer $sharedFb
     * @return Post
     */
    public function setSharedFb($sharedFb)
    {
        $this->sharedFb = $sharedFb;

        return $this;
    }

    /**
     * Get sharedFb
     *
     * @return integer 
     */
    public function getSharedFb()
    {
        return (int)$this->sharedFb;
    }

    /**
     * Set sharedTw
     *
     * @param integer $sharedTw
     * @return Post
     */
    public function setSharedTw($sharedTw)
    {
        $this->sharedTw = $sharedTw;

        return $this;
    }

    /**
     * Get sharedTw
     *
     * @return integer 
     */
    public function getSharedTw()
    {
        return (int)$this->sharedTw;
    }

    /**
     * Remove tags
     *
     * @param \Acme\MainBundle\Entity\Tag $tags
     */
    public function removeTag(\Acme\MainBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    public function getNotEmptyShortText()
    {
       return  ($this->getShortText() == null) ? 'Нет описания' : $this->getShortText();
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return Post
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
     * Set video
     *
     * @param string $video
     * @return Post
     */
    public function setVideo($video)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get video
     *
     * @return string 
     */
    public function getVideo()
    {
        return $this->video;
    }

    public function getArrayVideoId()
    {
        $ids = array();
        $videos = explode(',', $this->getVideo());
        foreach ($videos as $video) {
            $video = trim($video);
            $part = parse_url($video);
            parse_str($part['query'], $query);
            if (isset($query['v']) && !empty($query['v'])) {
                $ids[] = $query['v'];
            }
        }

        return $ids;
    }

    /**
     * Set imageAlt
     *
     * @param string $imageAlt
     * @return Post
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
}
