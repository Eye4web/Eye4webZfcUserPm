<?php

namespace Eye4web\ZfcUserPmTest\View\Helper;

use Eye4web\ZfcUser\Pm\Entity\Conversation;
use Eye4web\ZfcUser\Pm\View\Helper\ZfcUserPmHelper;
use PHPUnit_Framework_TestCase;
use ZfcUser\Entity\User;

class ZfcUserPmHelperTest extends PHPUnit_Framework_TestCase
{
    /** @var ZfcUserPmHelper */
    protected $helper;

    /** @var \Eye4web\ZfcUser\Pm\View\Helper\ZfcUserPmHelper */
    protected $pmService;

    /** @var \ZfcUser\Mapper\UserInterface $userMapperInterface */
    protected $userMapperInterface;

    public function setUp()
    {
        /** @var \Eye4web\Zf2User\Pm\Service\AuthorService $pmService */
        $pmService = $this->getMockBuilder('Eye4web\ZfcUser\Pm\Service\PmService')
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->pmService = $pmService;

        /** @var \ZfcUser\Mapper\UserInterface $userMapperInterface */
        $userMapperInterface = $this->getMock('ZfcUser\Mapper\UserInterface');
        $this->userMapperInterface = $userMapperInterface;

        $helper = new ZfcUserPmHelper($this->pmService, $this->userMapperInterface);
        $this->helper = $helper;
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\View\Helper\ZfcUserPmHelper::__construct
     */
    public function testConstruct()
    {
        $helper = new ZfcUserPmHelper($this->pmService, $this->userMapperInterface);
        $this->assertInstanceOf('Eye4web\ZfcUser\Pm\View\Helper\ZfcUserPmHelper', $helper);
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\View\Helper\ZfcUserPmHelper::__invoke
     */
    public function testInvokeInstanceOfZfcUserPmHelper()
    {
        $helper = new ZfcUserPmHelper($this->pmService, $this->userMapperInterface);
        $this->assertInstanceOf('Eye4web\ZfcUser\Pm\View\Helper\ZfcUserPmHelper', $helper->__invoke());
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\View\Helper\ZfcUserPmHelper::getUser
     */
    public function testGetUser()
    {
        $id = 1;

        $this->userMapperInterface->expects($this->once())
                            ->method('findById')
                            ->with($id);

        $this->helper->getUser($id);
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\View\Helper\ZfcUserPmHelper::getParticipants
     */
    public function testGetParticipants()
    {
        $id = 1;
        $conversation = new Conversation();
        $conversation->setId('045a4049-7c37-4053-97eb-7c6e8d1c1d64');

        $user1 = new User();
        $user2 = new User();
        $user3 = new User();

        $this->pmService->expects($this->once())
                            ->method('getParticipants')
                            ->with($conversation)
                            ->will($this->returnValue(array(
            $user1,
            $user2,
            $user3,
        )));

        $participants = $this->helper->getParticipants($conversation);
        $this->assertCount(3, $participants);
        foreach ($participants as $user) {
            $this->assertInstanceOf('ZfcUser\Entity\UserInterface', $user);
        }
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\View\Helper\ZfcUserPmHelper::getLastReply
     */
    public function testGetLastReply()
    {
        $id = 1;
        $conversation = new Conversation();
        $conversation->setId('045a4049-7c37-4053-97eb-7c6e8d1c1d64');

        $this->pmService->expects($this->once())
                            ->method('getLastReply')
                            ->with($conversation);

        $this->helper->getLastReply($conversation);
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\View\Helper\ZfcUserPmHelper::isUnread
     */
    public function testGetUnread()
    {
        $id = 1;
        $conversation = new Conversation();
        $conversation->setId('045a4049-7c37-4053-97eb-7c6e8d1c1d64');

        $user = new User();

        $this->pmService->expects($this->once())
                            ->method('isUnread')
                            ->with($conversation, $user)
                            ->will($this->returnValue(true));

        $this->assertTrue($this->helper->isUnread($conversation, $user));
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\View\Helper\ZfcUserPmHelper::getUnreadConversations
     */
    public function testGetUnreadConversations()
    {
        $user = new User();

        $conversation1 = new Conversation();
        $conversation2 = new Conversation();
        $conversation3 = new Conversation();

        $this->pmService->expects($this->once())
                            ->method('getUnreadConversations')
                            ->with($user)
                            ->will($this->returnValue(array(
            $conversation1,
            $conversation2,
            $conversation3,
        )));

        $conversations = $this->helper->getUnreadConversations($user);
        $this->assertCount(3, $conversations);
        foreach ($conversations as $conversation) {
            $this->assertInstanceOf('Eye4web\ZfcUser\Pm\Entity\ConversationInterface', $conversation);
        }
    }
}
