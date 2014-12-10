<?php

namespace Eye4web\Zf2BoardTest\Factory\Mapper\DoctrineORM;

use Eye4web\ZfcUser\Pm\Factory\Mapper\DoctrineORM\PmMapperFactory;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

class PmMapperFactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var PostMapperFactory */
    protected $factory;

    /** @var ServiceLocatorInterface */
    protected $serviceLocator;

    public function setUp()
    {
        /** @var ServiceLocatorInterface $serviceLocator */
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->serviceLocator = $serviceLocator;

        $factory = new PmMapperFactory;
        $this->factory = $factory;
    }

    public function testCreateService()
    {
        $moduleOptions = $this->getMockBuilder('Eye4web\ZfcUser\Pm\Options\ModuleOptions')
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->serviceLocator->expects($this->at(0))
                             ->method('get')
                             ->with('Eye4web\ZfcUser\Pm\Options\ModuleOptions')
                             ->willReturn($moduleOptions);
                             
        $objectManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->serviceLocator->expects($this->at(1))
                             ->method('get')
                             ->with('Doctrine\ORM\EntityManager')
                             ->willReturn($objectManager);
                        
        $options = $this->getMock('ZfcUser\Options\ModuleOptions');     
        $this->serviceLocator->expects($this->at(2))
                             ->method('get')
                             ->with('zfcuser_module_options')
                             ->will($this->returnValue($options));

        $result = $this->factory->createService($this->serviceLocator);

        $this->assertInstanceOf('Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper', $result);
    }
}
