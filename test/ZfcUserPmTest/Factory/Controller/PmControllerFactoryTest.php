<?php

namespace Eye4web\Zf2UserPmTest\Factory\Controller;

use Eye4web\ZfcUser\Pm\Factory\Controller\PmControllerFactory;
use Zend\Mvc\Controller\ControllerManager;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

class PmControllerFactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var PmControllerFactory */
    protected $factory;

    /** @var ControllerManager */
    protected $controllerManager;

    /** @var ServiceLocatorInterface */
    protected $serviceLocator;

    public function setUp()
    {
        /** @var ControllerManager $controllerManager */
        $controllerManager = $this->getMock('Zend\Mvc\Controller\ControllerManager');
        $this->controllerManager = $controllerManager;

        /** @var ServiceLocatorInterface $serviceLocator */
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->serviceLocator = $serviceLocator;

        $controllerManager->expects($this->any())
                          ->method('getServiceLocator')
                          ->willReturn($serviceLocator);

        $factory = new PmControllerFactory();
        $this->factory = $factory;
    }

     /**
     * @covers Eye4web\ZfcUser\Pm\Factory\Controller\PmControllerFactory::createService
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

        $newConversationFormService = $this->getMockBuilder('Eye4web\ZfcUser\Pm\Form\NewConversationForm')
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->serviceLocator->expects($this->at(1))
                             ->method('get')
                             ->with('Eye4web\ZfcUser\Pm\Form\NewConversationForm')
                             ->willReturn($newConversationFormService);

        $newMessageForm = $this->getMockBuilder('Eye4web\ZfcUser\Pm\Form\NewMessageForm')
                            ->disableOriginalConstructor()
                            ->getMock();

        $this->serviceLocator->expects($this->at(2))
                             ->method('get')
                             ->with('Eye4web\ZfcUser\Pm\Form\NewMessageForm')
                             ->willReturn($newMessageForm);

        $deleteConversationForm = $this->getMockBuilder('Eye4web\ZfcUser\Pm\Form\DeleteConversationsForm')
                                ->disableOriginalConstructor()
                                ->getMock();

        $this->serviceLocator->expects($this->at(3))
                             ->method('get')
                             ->with('Eye4web\ZfcUser\Pm\Form\DeleteConversationsForm')
                             ->willReturn($deleteConversationForm);

        $moduleOptions = $this->getMockBuilder('Eye4web\ZfcUser\Pm\Options\ModuleOptions')
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->serviceLocator->expects($this->at(4))
                             ->method('get')
                             ->with('Eye4web\ZfcUser\Pm\Options\ModuleOptions')
                             ->willReturn($moduleOptions);

        $eventManager = $this->getMockBuilder('Zend\EventManager\EventManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->serviceLocator->expects($this->at(5))
            ->method('get')
            ->with('EventManager')
            ->willReturn($eventManager);

        $result = $this->factory->createService($this->controllerManager);

        $this->assertInstanceOf('Eye4web\ZfcUser\Pm\Controller\PmController', $result);
    }
}
