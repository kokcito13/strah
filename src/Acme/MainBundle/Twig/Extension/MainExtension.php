<?php

namespace Acme\MainBundle\Twig\Extension;

use Acme\MainBundle\Service\CityService;
use CG\Core\ClassUtils;

class MainExtension extends \Twig_Extension
{
    protected $cityService = null;

    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('currentCity', array($this, 'getCurrentCity')),
            new \Twig_SimpleFunction('cityList', array($this, 'getCityList')),
        );
    }

    public function getCityList()
    {
        return $this->cityService->getCityList();
    }

    public function getCurrentCity()
    {
        return $this->cityService->getCity();
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'main';
    }
}
