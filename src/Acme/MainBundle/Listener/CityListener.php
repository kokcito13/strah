<?php
namespace Acme\MainBundle\Listener;

use Acme\MainBundle\Service\CityService;
use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Tests\EventListener\TestKernelThatThrowsException;

class CityListener
{

    protected $em;
    protected $cityService;

    public function __construct(EntityManager $em, CityService $cityService)
    {
        $this->em = $em;
        $this->cityService = $cityService;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $params = $request->attributes->get('_route_params');

        if (isset($params['city_url']) && !empty($params['city_url'])) {
            $this->cityService->setCityByUrl($params['city_url']);
        }
    }
}