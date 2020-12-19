<?php

namespace Eye4web\ZfcUser\Pm\Factory\Service;

use Eye4web\ZfcUser\Pm\Service\PmService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PmServiceFactory implements \Zend\ServiceManager\Factory\FactoryInterface
{
    /**
     * Create mapper
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return PmService
     */
    public function __invoke(\Interop\Container\ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        /**
         * @var ModuleOptions $moduleOptions
         */
        $moduleOptions = $serviceLocator->get('Eye4web\ZfcUser\Pm\Options\ModuleOptions');

        $mapper = new PmService($serviceLocator->get($moduleOptions->getPmMapper()), $moduleOptions);

        return $mapper;
    }
}
