<?php

namespace Eye4web\Zf2UserPmTest\Factory\View\Helper;

use Eye4web\ZfcUser\Pm\Factory\View\Helper\ZfcUserPmHelperFactory;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

class ZfcUserPmHelperFactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var ZfcUserPmHelperFactory */
    protected $factory;

    /** @var ServiceLocatorInterface */
    protected $helperManager;

    /** @var ServiceLocatorInterface */
    protected $serviceLocator;

    public function setUp()
    {
        /** @var ServiceLocatorInterface $serviceLocator */
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->serviceLocator = $serviceLocator;

        /** @var ServiceLocatorInterface $helperManager */
        $helperManager = $this->getMockBuilder('Zend\View\HelperPluginManager')
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->helperManager = $helperManager;

        $helperManager->expects($this->once())
                       ->method('getServiceLocator')
                       ->willReturn($serviceLocator);

        $factory = new ZfcUserPmHelperFactory();
        $this->factory = $factory;
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Factory\View\Helper\ZfcUserPmHelperFactory::createService
     */
    public function testCreateService()
    {
        $pmService = $this->getMockBuilder('Eye4web\ZfcUser\Pm\Service\PmService')
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->serviceLocator->expects($this->at(0))
                             ->method('get')
                             ->with('Eye4web\ZfcUser\Pm\Service\PmService')
                             ->willReturn($pmService);

        $user = $this->getMockForAbstractClass('ZfcUser\Mapper\UserInterface');
        $this->serviceLocator->expects($this->at(1))
                             ->method('get')
                             ->with('zfcuser_user_mapper')
                             ->will($this->returnValue($user));

        $result = $this->factory->createService($this->helperManager);

        $this->assertInstanceOf('Eye4web\ZfcUser\Pm\View\Helper\ZfcUserPmHelper', $result);
    }
}
