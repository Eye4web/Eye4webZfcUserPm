<?php

namespace Eye4web\ZfcUser\Pm\Factory\View\Helper;

use Eye4web\ZfcUser\Pm\View\Helper\ZfcUserPmHelper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ZfcUserPmHelperFactory implements \Zend\ServiceManager\Factory\FactoryInterface
{
    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $helperLocator
     * @return ConfigHelper|mixed
     */
    public function __invoke(\Psr\Container\ContainerInterface $helperLocator, $requestedName, array $options = null)
    {
        /**
         * @var ServiceLocatorInterface $serviceLocator
         */
        $serviceLocator = $helperLocator;

        $pmService = $serviceLocator->get('Eye4web\ZfcUser\Pm\Service\PmService');

        $zfcUserMapper = $serviceLocator->get('zfcuser_user_mapper');

        return new ZfcUserPmHelper($pmService, $zfcUserMapper);
    }
}
