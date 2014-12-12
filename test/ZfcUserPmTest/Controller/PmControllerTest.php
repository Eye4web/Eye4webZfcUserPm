<?php

namespace Eye4web\ZfcUser\PmTest\Controller;

use Eye4web\ZfcUser\Pm\Controller\PmController;
use PHPUnit_Framework_TestCase;

class PmControllerTest extends PHPUnit_Framework_TestCase
{
    /** @var \Eye4web\ZfcUser\Pm\Controller\PmController */
    protected $controller;

    /** @var \Eye4web\ZfcUser\Pm\Service\PmService */
    protected $pmService;

    /** @var \Zend\Mvc\Controller\PluginManager */
    protected $pluginManager;

    /** @var \Eye4web\ZfcUser\Pm\Form\NewConversationForm */
    protected $newConversationForm;

    /** @var \Eye4web\ZfcUser\Pm\Form\NewMessageForm */
    protected $newMessageForm;

    /** @var \Eye4web\ZfcUser\Pm\Form\DeleteConversationsForm */
    protected $deleteConversationsForm;

    /** @var \Eye4web\ZfcUser\Pm\Options\ModuleOptions */
    protected $moduleOptions;

    /** @var \ZfcUser\Options\ModuleOptions */
    protected $zfcUserModuleOptions;

    /** @var \Zend\EventManager\EventManager */
    protected $eventManager;

    public $pluginManagerPlugins = [];

    public function setUp()
    {
        /** @var \Eye4web\ZfcUser\Pm\Service\PmService $pmService */
        $pmService = $this->getMockBuilder('Eye4web\ZfcUser\Pm\Service\PmService')
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->pmService = $pmService;

        /** @var \Zend\Mvc\Controller\PluginManager $pluginManager */
        $pluginManager = $this->getMock('Zend\Mvc\Controller\PluginManager', array('get'));

        $pluginManager->expects($this->any())
                      ->method('get')
                      ->will($this->returnCallback(array($this, 'helperMockCallbackPluginManagerGet')));

        $this->pluginManager = $pluginManager;

        /** @var \Eye4web\ZfcUser\Pm\Form\NewConversationForm $newConversationForm */
        $newConversationForm = $this->getMockBuilder('Eye4web\ZfcUser\Pm\Form\NewConversationForm')
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->newConversationForm = $newConversationForm;

        /** @var \Eye4web\ZfcUser\Pm\Form\NewMessageForm $newMessageForm */
        $newMessageForm = $this->getMockBuilder('Eye4web\ZfcUser\Pm\Form\NewMessageForm')
                            ->disableOriginalConstructor()
                            ->getMock();

        $this->newMessageForm = $newMessageForm;

        /** @var \Eye4web\ZfcUser\Pm\Form\DeleteConversationsForm $deleteConversationsForm */
        $deleteConversationsForm = $this->getMockBuilder('Eye4web\ZfcUser\Pm\Form\DeleteConversationsForm')
                                ->disableOriginalConstructor()
                                ->getMock();

        $this->deleteConversationsForm = $deleteConversationsForm;

        /** @var \Eye4web\ZfcUser\Pm\Options\ModuleOptions $moduleOptions */
        $moduleOptions = $this->getMockBuilder('Eye4web\ZfcUser\Pm\Options\ModuleOptions')
                               ->disableOriginalConstructor()
                               ->getMock();

        $this->moduleOptions = $moduleOptions;

        $zfcModuleOptions = $this->getMockBuilder('ZfcUser\Options\ModuleOptions')
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->zfcModuleOptions = $zfcModuleOptions;

        $eventManager = $this->getMock('Zend\EventManager\EventManager');
        $this->eventManager = $eventManager;

        $controller = new PmController(
            $pmService,
            $newConversationForm,
            $newMessageForm,
            $deleteConversationsForm,
            $moduleOptions,
            $zfcModuleOptions
        );

        $controller->setPluginManager($pluginManager);
        $controller->setEventManager($eventManager);

        $this->controller = $controller;
    }

    public function testFoo()
    {
        return $this->assertTrue(true);
    }
}
