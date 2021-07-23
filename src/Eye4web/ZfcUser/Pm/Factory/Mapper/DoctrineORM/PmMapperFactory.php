<?php

namespace Eye4web\ZfcUser\Pm\Factory\Mapper\DoctrineORM;

use Doctrine\ORM\EntityManager;
use Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper;
use Eye4web\ZfcUser\Pm\Options\ModuleOptions;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcUser\Options\ModuleOptions as ZfcUserModuleOptions;

class PmMapperFactory implements \Zend\ServiceManager\Factory\FactoryInterface
{
    /**
     * Create mapper
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return PmMapper
     */
    public function __invoke(\Psr\Container\ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        /**
         * @var ModuleOptions $moduleOptions
         */
        $moduleOptions = $serviceLocator->get('Eye4web\ZfcUser\Pm\Options\ModuleOptions');

        /**
         * @var EntityManager $objectManager
         */
        $objectManager = $serviceLocator->get('Doctrine\ORM\EntityManager');

        /**
         * @var ZfcUserModuleOptions $zfcUserOptions
         */
        $zfcUserOptions = $serviceLocator->get('zfcuser_module_options');

        $mapper = new PmMapper($objectManager, $moduleOptions, $zfcUserOptions);

        return $mapper;
    }
}
