<?php
namespace Acme\MainBundle\Service;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RegisteringUserSocial
{
    protected $em;

    private $socialType;
    private $socialId;
    private $socialImage;
    private $socialLastName;
    private $socialFirstName;

    const SOCIAL_VK = 'vk';
    const SOCIAL_FB = 'fb';

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function registration($data)
    {
        if (empty($data)) {
            throw new NotFoundHttpException('Что то не так');
        }
        $this->setInfo($data);

        if ($user = $this->checkUserExist()) {
            return $user;
        }

        return $this->addNewUser();
    }

    public function setInfo($data)
    {
        if (!isset($data['type']))
            throw new NotFoundHttpException('Что то не так 2');

        switch ($data['type']) {
            case self::SOCIAL_VK:
                $this->socialLastName = $data['lastName'];
                $this->socialFirstName = $data['firstName'];
                break;
            case self::SOCIAL_FB:
                $partsName = explode(' ', $data['fullName']);
                $this->socialLastName = $partsName[1];
                $this->socialFirstName = $partsName[0];
                break;
            default:
                throw new NotFoundHttpException('Что то не так 3');
                break;
        }

        $this->socialId = $data['uId'];
        $this->socialImage = $data['image'];
        $this->socialType = $data['type'];
    }

    public function checkUserExist()
    {
        switch ($this->socialType) {
            case self::SOCIAL_VK:
                $type = 'vkId';
                break;
            case self::SOCIAL_FB:
                $type = 'facebookUid';
                break;
            default:
                throw new NotFoundHttpException('Что то не так 4');
                break;
        }

        return $this->em->getRepository('ApplicationSonataUserBundle:User')->findOneBy(array($type => $this->socialId));
    }

    public function addNewUser()
    {
        $user = new User();
        $user->setFirstname($this->socialFirstName);
        $user->setLastname($this->socialLastName);
        $user->setSocialPicture($this->socialImage);
        $user->setEmail($this->socialId.'@time.com');
        $user->setEmailCanonical($this->socialId.'@time.com');
        $user->setUsername($this->socialId);
        $user->setUsernameCanonical($this->socialId);
        $user->setEnabled(false);
        $user->setPassword(md5($this->socialId));

        /*
         * $plainPassword = 'ryanpass';
         * $encoder = $this->container->get('security.password_encoder');
         * $encoded = $encoder->encodePassword($user, $plainPassword);

         * $user->setPassword($encoded);
         */


        switch ($this->socialType) {
            case self::SOCIAL_VK:
                $user->setVkId($this->socialId);
                break;
            case self::SOCIAL_FB:
                $user->setFacebookUid($this->socialId);
                break;
            default:
                throw new NotFoundHttpException('Что то не так 4');
                break;
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}