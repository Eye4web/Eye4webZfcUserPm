<?php

namespace Eye4web\ZfcUserPmTest\Form;

use Eye4web\ZfcUser\Pm\Options\ModuleOptions;
use PHPUnit_Framework_TestCase;

class ModuleOptionsTest extends PHPUnit_Framework_TestCase
{
    /** @var \Eye4web\ZfcUser\Pm\Options\ModuleOptions */
    protected $options;

    public function setUp()
    {
        $options = new ModuleOptions([]);
        $this->options = $options;
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Options\ModuleOptions::setConversationsPerPage
     * @covers Eye4web\ZfcUser\Pm\Options\ModuleOptions::getConversationsPerPage
     */
    public function testSetGetConversationsPerPage()
    {
        $this->options->setConversationsPerPage(1);
        $this->assertEquals(1, $this->options->getConversationsPerPage());
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Options\ModuleOptions::setMessageSortOrder
     * @covers Eye4web\ZfcUser\Pm\Options\ModuleOptions::getMessageSortOrder
     */
    public function testSetGetMessageSortOrder()
    {
        $this->options->setMessageSortOrder('ASC');
        $this->assertEquals('ASC', $this->options->getMessageSortOrder());
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Options\ModuleOptions::setMessagesPerPage
     * @covers Eye4web\ZfcUser\Pm\Options\ModuleOptions::getMessagesPerPage
     */
    public function testSetGetMessagesPerPage()
    {
        $this->options->setMessagesPerPage(10);
        $this->assertEquals(10, $this->options->getMessagesPerPage());
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Options\ModuleOptions::setConversationEntity
     * @covers Eye4web\ZfcUser\Pm\Options\ModuleOptions::getConversationEntity
     */
    public function testSetGetConversationEntity()
    {
        $this->options->setConversationEntity('Eye4web\ZfcUser\Pm\Entity\Conversation');
        $this->assertEquals('Eye4web\ZfcUser\Pm\Entity\Conversation', $this->options->getConversationEntity());
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Options\ModuleOptions::setConversationReceiverEntity
     * @covers Eye4web\ZfcUser\Pm\Options\ModuleOptions::getConversationReceiverEntity
     */
    public function testSetGetConversationReceiverEntity()
    {
        $this->options->setConversationReceiverEntity('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver');
        $this->assertEquals('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver', $this->options->getConversationReceiverEntity());
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Options\ModuleOptions::setMessageEntity
     * @covers Eye4web\ZfcUser\Pm\Options\ModuleOptions::getMessageEntity
     */
    public function testSetGetMessageEntity()
    {
        $this->options->setMessageEntity('Eye4web\ZfcUser\Pm\Entity\Message');
        $this->assertEquals('Eye4web\ZfcUser\Pm\Entity\Message', $this->options->getMessageEntity());
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Options\ModuleOptions::setPmMapper
     * @covers Eye4web\ZfcUser\Pm\Options\ModuleOptions::getPmMapper
     */
    public function testSetGetPmMapper()
    {
        $this->options->setPmMapper('Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper');
        $this->assertEquals('Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper', $this->options->getPmMapper());
    }
}
