<?php

namespace Eye4web\ZfcUser\Pm\Factory\Service;

use Eye4web\ZfcUser\Pm\Service\PmService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PmServiceFactory implements FactoryInterface
{
    /**
     * Create mapper
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return PmService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $serviceLocator->get('Eye4web\ZfcUser\Pm\Options\ModuleOptions');

        $mapper = new PmService($serviceLocator->get($moduleOptions->getPmMapper()));
        return $mapper;
    }
}
