<?php

namespace Eye4web\ZfcUser\Pm\Factory\Options;

use Eye4web\ZfcUser\Pm\Options\ModuleOptions;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ModuleOptionsFactory implements \Zend\ServiceManager\Factory\FactoryInterface
{
    /**
     * Create options
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return ModuleOptions
     */
    public function __invoke(\Psr\Container\ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $config = $serviceLocator->get('Config');

        $moduleConfig = [];
        if (isset($config['eye4web']['zfc-user']['pm'])) {
            $moduleConfig = $config['eye4web']['zfc-user']['pm'];
        }

        $service = new ModuleOptions($moduleConfig);

        return $service;
    }
}
