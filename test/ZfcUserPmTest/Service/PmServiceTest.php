<?php

namespace Eye4web\ZfcUserPmTest\Service;

use Eye4web\ZfcUser\Pm\Entity\Conversation;
use Eye4web\ZfcUser\Pm\Entity\Message;
use Eye4web\ZfcUser\Pm\Service\PmService;
use PHPUnit_Framework_TestCase;
use ZfcUser\Entity\User;

class PmServiceTest extends PHPUnit_Framework_TestCase
{
    /** @var PmService */
    protected $service;

    /** @var \Eye4web\ZfcUser\Pm\Mapper\PmMapperInterface */
    protected $mapper;

    public function setUp()
    {
        /** @var \Eye4web\Zf2Board\Mapper\PostMapperInterface $mapper */
        $mapper = $this->getMock('\Eye4web\ZfcUser\Pm\Mapper\PmMapperInterface');
        $this->mapper = $mapper;

        $service = new PmService($mapper);
        $this->service = $service;
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Service\PmService::__construct
     */
    public function testGetConstruct()
    {
        $service = new PmService($this->mapper);
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Service\PmService::getUserConversations
     */
    public function testGetUserConversations()
    {
        $conversation1 = new Conversation();
        $conversation2 = new Conversation();
        $conversation3 = new Conversation();

        $id = 1;

        $this->mapper->expects($this->once())
                            ->method('getUserConversations')
                            ->with($id)
                            ->will($this->returnValue(array(
            $conversation1,
            $conversation2,
            $conversation3,
        )));

        $conversations = $this->service->getUserConversations($id);
        $this->assertCount(3, $conversations);
        foreach ($conversations as $conversation) {
            $this->assertInstanceOf('Eye4web\ZfcUser\Pm\Entity\ConversationInterface', $conversation);
        }
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Service\PmService::getMessages
     */
    public function testGetMessages()
    {
        $conversation = new Conversation();

        $message1 = new Message();
        $message2 = new Message();
        $message3 = new Message();

        $this->mapper->expects($this->once())
                            ->method('getMessages')
                            ->with($conversation)
                            ->will($this->returnValue(array(
            $message1,
            $message2,
            $message3,
        )));

        $messages = $this->service->getMessages($conversation);
        $this->assertCount(3, $messages);
        foreach ($messages as $message) {
            $this->assertInstanceOf('Eye4web\ZfcUser\Pm\Entity\MessageInterface', $message);
        }
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Service\PmService::markRead
     */
    public function testMarkRead()
    {
        $conversation = new Conversation();

        $message1 = new Message();
        $message2 = new Message();
        $message3 = new Message();

        $user = new User();

        $this->mapper->expects($this->once())
                            ->method('markRead')
                            ->with($conversation, $user);

        $messages = $this->service->markRead($conversation, $user);
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Service\PmService::getUsers
     */
    public function testGetUsers()
    {
        $user1 = new User();
        $user1->setId(1);
        $user1->setDisplayName('abdul malik');

        $user2 = new User();
        $user2->setId(2);
        $user2->setDisplayName('ikhsan');

        $returnValue = array(
            0 => array(
                'id' => 1,
                'text' => 'abdul malik',
            ),
            1 => array(
                'id' => 2,
                'text' => 'ikhsan',
            ),
        );

        $this->mapper->expects($this->once())
                            ->method('getUsers')
                            ->will($this->returnValue(array(
            $user1,
            $user2,
        )));

        $users = $this->service->getUsers();
        $this->assertCount(2, $users);
        $this->assertEquals(array('id' => $user1->getId(), 'text' => $user1->getDisplayName()), $users[0]);
        $this->assertEquals(array('id' => $user2->getId(), 'text' => $user2->getDisplayName()), $users[1]);
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Service\PmService::getConversation
     */
    public function testGetConversation()
    {
        $conversation = new Conversation();

        $id = 1;
        $this->mapper->expects($this->once())
                            ->method('getConversation')
                            ->with($id)
                            ->will($this->returnValue($conversation));

        $this->assertInstanceOf('Eye4web\ZfcUser\Pm\Entity\ConversationInterface', $this->service->getConversation($id));
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Service\PmService::getParticipants
     */
    public function testGetParticipants()
    {
        $conversation = new Conversation();

        $user1 = new User();
        $user2 = new User();

        $this->mapper->expects($this->once())
                            ->method('getParticipants')
                            ->with($conversation)
                            ->will($this->returnValue(array(
            $user1,
            $user2,
        )));

        $participants = $this->service->getParticipants($conversation);
        $this->assertCount(2, $participants);
        foreach ($participants as $user) {
            $this->assertInstanceOf('ZfcUser\Entity\UserInterface', $user);
        }
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Service\PmService::isUnread
     */
    public function testIsUnread()
    {
        $conversation = new Conversation();
        $user = new User();

        $this->mapper->expects($this->once())
                            ->method('isUnread')
                            ->with($conversation, $user)
                            ->will($this->returnValue(true));

        $this->assertTrue($this->service->isUnread($conversation, $user));
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Service\PmService::markUnread
     */
    public function testMarkUnread()
    {
        $conversation = new Conversation();
        $user = new User();

        $this->mapper->expects($this->once())
                            ->method('markUnread')
                            ->with($conversation);

        $this->service->markUnread($conversation);
    }
}
