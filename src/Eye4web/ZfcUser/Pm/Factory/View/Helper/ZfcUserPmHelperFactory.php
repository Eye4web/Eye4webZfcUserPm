<?php

namespace Eye4web\ZfcUser\Pm\Factory\View\Helper;

use Eye4web\ZfcUser\Pm\View\Helper\ZfcUserPmHelper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ZfcUserPmHelperFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $helperLocator
     * @return ConfigHelper|mixed
     */
    public function createService(ServiceLocatorInterface $helperLocator)
    {
        /** @var ServiceLocatorInterface $serviceLocator */
        $serviceLocator = $helperLocator->getServiceLocator();

        $pmService = $serviceLocator->get('Eye4web\ZfcUser\Pm\Service\PmService');

        $zfcUserMapper = $serviceLocator->get('zfcuser_user_mapper');

        return new ZfcUserPmHelper($pmService, $zfcUserMapper);
    }
}
