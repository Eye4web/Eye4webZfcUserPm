<?php

namespace Eye4web\Zf2UserPmTest\Factory\Service;

use Eye4web\ZfcUser\Pm\Factory\Service\PmServiceFactory;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

class PmServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var PmServiceFactory */
    protected $factory;

    /** @var ServiceLocatorInterface */
    protected $serviceLocator;

    public function setUp()
    {
        /** @var ServiceLocatorInterface $serviceLocator */
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->serviceLocator = $serviceLocator;

        $factory = new PmServiceFactory();
        $this->factory = $factory;
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Factory\Service\PmServiceFactory::createService
     */
    public function testCreateService()
    {
        $options = $this->getMock('Eye4web\ZfcUser\Pm\Options\ModuleOptions');

        $this->serviceLocator->expects($this->at(0))
                             ->method('get')
                             ->with('Eye4web\ZfcUser\Pm\Options\ModuleOptions')
                             ->willReturn($options);

        $options->expects($this->once())
                ->method('getPmMapper')
                ->will($this->returnValue('Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper'));

        $pmMapperMock = $this->getMockBuilder('Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper')
                ->disableOriginalConstructor()
                ->getMock();

        $this->serviceLocator->expects($this->at(1))
                             ->method('get')
                             ->with('Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper')
                             ->willReturn($pmMapperMock);

        $result = $this->factory->createService($this->serviceLocator);

        $this->assertInstanceOf('Eye4web\ZfcUser\Pm\Service\PmService', $result);
    }
}
