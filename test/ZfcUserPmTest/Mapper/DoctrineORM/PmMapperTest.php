<?php

namespace Eye4web\ZfcUserPmTest\Mapper\DoctrineORM;

use Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper;
use PHPUnit_Framework_TestCase;
use ZfcUser\Entity\User;
use Eye4web\ZfcUser\Pm\Entity\Conversation;
use Eye4web\ZfcUser\Pm\Entity\ConversationReceiver;
use Eye4web\ZfcUser\Pm\Entity\Message;

class PmMapperTest extends PHPUnit_Framework_TestCase
{
    /** @var PmMapper */
    protected $mapper;

    /** @var \Doctrine\ORM\EntityManager */
    protected $objectManager;

    /** @var \Eye4web\ZfcUser\Pm\Options\ModuleOptionsInterface */
    protected $options;

    /** @var \ZfcUser\Options\ModuleOptions */
    protected $zfcuserOptions;

    public function setUp()
    {
        /** @var \Doctrine\ORM\EntityManager $objectManager */
        $objectManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->objectManager = $objectManager;

        /** @var \Eye4web\ZfcUser\Pm\Options\ModuleOptionsInterface $options */
        $options = $this->getMock('Eye4web\ZfcUser\Pm\Options\ModuleOptions');
        $this->options = $options;

        $zfcuserOptions = $this->getMock('ZfcUser\Options\ModuleOptions');
        $this->zfcuserOptions = $zfcuserOptions;

        $mapper = new PmMapper($objectManager, $options, $zfcuserOptions);

        $this->mapper = $mapper;
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper::__construct
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('Eye4web\ZfcUser\Pm\Mapper\PmMapperInterface', new PmMapper($this->objectManager, $this->options, $this->zfcuserOptions));
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper::getUsers
     */
    public function testGetUsers()
    {
        $objectRepository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');

        $this->zfcuserOptions->expects($this->once())
             ->method('getUserEntityClass')
             ->will($this->returnValue('ZfcUser\Entity\User'));

        $this->objectManager->expects($this->once())
                            ->method('getRepository')
                            ->with('ZfcUser\Entity\User')
                            ->will($this->returnValue($objectRepository));

        $user1 = new User();
        $user2 = new User();

        $objectRepository->expects($this->once())
                   ->method('findAll')
                   ->will($this->returnValue([
            $user1,
            $user2,
        ]));

        $users = $this->mapper->getUsers();
        $this->assertCount(2, $users);
        foreach ($users as $user) {
            $this->assertInstanceOf('ZfcUser\Entity\UserInterface', $user);
        }
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper::getUnreadConversations
     */
    public function testGetUnreadConversations()
    {
        $user = new User();
        $user->setId(1);

        $optionsData = [
            'to' => $user->getId(),
            'unread' => true,
            'deleted' => false,
        ];

        $this->options->expects($this->once())
             ->method('getConversationReceiverEntity')
             ->will($this->returnValue('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver'));

        $objectRepository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');

        $this->objectManager->expects($this->once())
                            ->method('getRepository')
                            ->with('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver')
                            ->will($this->returnValue($objectRepository));

        $conversationReceiver1 = new ConversationReceiver();
        $conversationReceiver1->setConversation(new Conversation());

        $conversationReceiver2 = new ConversationReceiver();
        $conversationReceiver2->setConversation(new Conversation());

        $objectRepository->expects($this->once())
                   ->method('findBy')
                   ->with($optionsData)
                   ->will($this->returnValue([
            $conversationReceiver1,
            $conversationReceiver2,
        ]));

        $unreadConversations = $this->mapper->getUnreadConversations($user);
        $this->assertCount(2, $unreadConversations);
        foreach ($unreadConversations as $unreadConversation) {
            $this->assertInstanceOf('Eye4web\ZfcUser\Pm\Entity\ConversationInterface', $unreadConversation);
        }
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper::getMessages
     */
    public function testGetMessages()
    {
        $conversation = new Conversation();
        $conversation->setId(1);

        $optionsData = [
            ['conversation' => $conversation->getId()],
            ['date' => $this->options->getMessageSortOrder()],
        ];

        $this->options->expects($this->once())
             ->method('getMessageEntity')
             ->will($this->returnValue('Eye4web\ZfcUser\Pm\Entity\Message'));

        $objectRepository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->objectManager->expects($this->once())
                            ->method('getRepository')
                            ->with('Eye4web\ZfcUser\Pm\Entity\Message')
                            ->will($this->returnValue($objectRepository));

        $message1 = new Message();
        $message2 = new Message();

        $objectRepository->expects($this->once())
                   ->method('findBy')
                   ->with($optionsData[0], $optionsData[1])
                   ->will($this->returnValue([
            $message1,
            $message2,
        ]));

        $messages = $this->mapper->getMessages($conversation);
        $this->assertCount(2, $messages);
        foreach ($messages as $message) {
            $this->assertInstanceOf('Eye4web\ZfcUser\Pm\Entity\MessageInterface', $message);
        }
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper::markRead
     */
    public function testMarkRead()
    {
        $conversation = new Conversation();
        $conversation->setId(1);

        $user = new User();
        $user->setId(1);

        $optionsData = [
            'conversation' => $conversation->getId(),
            'to' => $user->getId(),
        ];

        $this->options->expects($this->once())
             ->method('getConversationReceiverEntity')
             ->will($this->returnValue('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver'));

        $objectRepository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->objectManager->expects($this->once())
                            ->method('getRepository')
                            ->with('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver')
                            ->will($this->returnValue($objectRepository));

        $conversationReceive = $this->getMock('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver');
        $objectRepository->expects($this->once())
                   ->method('findOneBy')
                   ->with($optionsData)
                   ->will($this->returnValue($conversationReceive));
        $conversationReceive->expects($this->once())
                    ->method('setUnread')
                    ->with(false);
        $objectRepository->expects($this->any())
                   ->method('flush');
        $this->mapper->markRead($conversation, $user);
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper::getParticipants
     */
    public function testGetParticipants()
    {
        $conversation = new Conversation();
        $conversation->setId(1);

        $optionsData = ['conversation' => $conversation->getId()];

        $this->options->expects($this->once())
             ->method('getConversationReceiverEntity')
             ->will($this->returnValue('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver'));

        $objectRepository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->objectManager->expects($this->once())
                            ->method('getRepository')
                            ->with('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver')
                            ->will($this->returnValue($objectRepository));

        $conversationReceivers[0] = new ConversationReceiver();
        $conversationReceivers[0]->setTo(2);
        $conversationReceivers[1] = new ConversationReceiver();
        $conversationReceivers[1]->setTo(3);

        $objectRepository->expects($this->any())
                   ->method('findBy')
                   ->with($optionsData)
                   ->will($this->returnValue([
            $conversationReceivers[0],
            $conversationReceivers[1],
        ]));

        $this->zfcuserOptions->expects($this->any())
             ->method('getUserEntityClass')
             ->will($this->returnValue('ZfcUser\Entity\User'));

        $participants = [];

        $user[0] = new User();
        $user[0]->setId(1);

        $user[1] = new User();
        $user[1]->setId(2);

        $participants = [];
        foreach ($conversationReceivers as $key => $conversationReceiver) {
            $participants[] = $user[$key];
            $objectRepository->expects($this->any())
                   ->method('find')
                   ->with('ZfcUser\Entity\User', $conversationReceiver->getTo())
                   ->will($this->returnValue($participants));
        }

        $participants = $this->mapper->getParticipants($conversation);
        $this->assertCount(2, $participants);
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper::getConversation
     */
    public function testGetConversation()
    {
        $conversation = new Conversation();
        $conversation->setId(1);

        $this->options->expects($this->once())
             ->method('getConversationEntity')
             ->will($this->returnValue('Eye4web\ZfcUser\Pm\Entity\Conversation'));

        $objectRepository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->objectManager->expects($this->any())
                            ->method('getRepository')
                            ->with('Eye4web\ZfcUser\Pm\Entity\Conversation')
                            ->will($this->returnValue($objectRepository));

        $conversation = $this->getMock('Eye4web\ZfcUser\Pm\Entity\Conversation');
        $objectRepository->expects($this->any())
                   ->method('find')
                   ->with('Eye4web\ZfcUser\Pm\Entity\Conversation', 1)
                   ->will($this->returnValue($conversation));

        $this->mapper->getConversation(1);
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper::getUserConversations
     */
    public function testGetUserConversations()
    {
        $optionsData = [
            'to' => 1,
            'deleted' => false,
        ];

        $this->options->expects($this->any())
             ->method('getConversationReceiverEntity')
             ->will($this->returnValue('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver'));

        $objectRepository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->objectManager->expects($this->any())
                            ->method('getRepository')
                            ->with('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver')
                            ->will($this->returnValue($objectRepository));

        $conversationReceivers[0] = new ConversationReceiver();
        $conversationReceivers[0]->setConversation(new Conversation());
        $conversationReceivers[1] = new ConversationReceiver();
        $conversationReceivers[1]->setConversation(new Conversation());

        $objectRepository->expects($this->any())
                   ->method('findBy')
                   ->with($optionsData)
                   ->will($this->returnValue([
            $conversationReceivers[0],
            $conversationReceivers[1],
        ]));

        $this->zfcuserOptions->expects($this->any())
             ->method('getUserEntityClass')
             ->will($this->returnValue('ZfcUser\Entity\User'));

        $participants = [];

        $user[0] = new User();
        $user[0]->setId(1);

        $user[1] = new User();
        $user[1]->setId(2);

        $conversations = [];
        foreach ($conversationReceivers as $key => $conversationReceiver) {
            $conversations[] = $conversationReceiver->getConversation();
        }

        $conversations = $this->mapper->getUserConversations(1);
        $this->assertCount(2, $conversations);
        foreach ($conversations as $conversation) {
            $this->assertInstanceOf('Eye4web\ZfcUser\Pm\Entity\ConversationInterface', $conversation);
        }
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper::getLastReply
     */
    public function testGetLastReply()
    {
        $conversation = new Conversation();
        $conversation->setId(1);

        $optionsData = [
            ['conversation' => $conversation->getId()],
            ['date' => 'DESC'],
        ];

        $this->options->expects($this->once())
             ->method('getMessageEntity')
             ->will($this->returnValue('Eye4web\ZfcUser\Pm\Entity\Message'));

        $objectRepository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->objectManager->expects($this->once())
                            ->method('getRepository')
                            ->with('Eye4web\ZfcUser\Pm\Entity\Message')
                            ->will($this->returnValue($objectRepository));

        $message = new Message();

        $objectRepository->expects($this->once())
                   ->method('findOneBy')
                   ->with($optionsData[0], $optionsData[1])
                   ->will($this->returnValue($message));

        $this->assertInstanceOf('Eye4web\ZfcUser\Pm\Entity\MessageInterface', $this->mapper->getLastReply($conversation));
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper::markUnread
     */
    public function testMarkUnread()
    {
        $conversation = new Conversation();
        $conversation->setId(1);

        $optionsData = [
            'conversation' => $conversation->getId(),
        ];

        $this->options->expects($this->any())
             ->method('getConversationReceiverEntity')
             ->will($this->returnValue('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver'));

        $objectRepository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->objectManager->expects($this->any())
                            ->method('getRepository')
                            ->with('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver')
                            ->will($this->returnValue($objectRepository));

        $conversationReceivers[0] = new ConversationReceiver();
        $conversationReceivers[0]->setConversation(new Conversation());
        $conversationReceivers[1] = new ConversationReceiver();
        $conversationReceivers[1]->setConversation(new Conversation());

        $objectRepository->expects($this->any())
                   ->method('findBy')
                   ->with($optionsData)
                   ->will($this->returnValue([
            $conversationReceivers[0],
            $conversationReceivers[1],
        ]));

        $conversationReceive = $this->getMock('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver');

        foreach ($conversationReceivers as $conversationReceiver) {
            $conversationReceive->expects($this->any())
                                ->method('setUnread')
                                ->with($this->returnValue(true));
            $objectRepository->expects($this->any())
                       ->method('persist')
                       ->with($conversationReceiver);
        }

        $objectRepository->expects($this->any())
                       ->method('flush')
                       ->will($this->returnValue($conversationReceive));

        $this->mapper->markUnread($conversation);
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper::newMessage
     */
    public function testNewMessage()
    {
        $conversation = new Conversation();
        $conversation->setId(1);

        $messageText =  'foo';

        $user = new User();
        $user->setId(1);

        $this->options->expects($this->any())
             ->method('getMessageEntity')
             ->will($this->returnValue('Eye4web\ZfcUser\Pm\Entity\Message'));

        $message = $this->getMock('Eye4web\ZfcUser\Pm\Entity\Message');
        $message->expects($this->any())
                ->method('setMessage')
                ->with($messageText)
                ->will($this->returnValue($message));
        $message->expects($this->any())
                ->method('setFrom')
                ->with($user->getId())
                ->will($this->returnValue($message));
        $message->expects($this->any())
                ->method('setConversation')
                ->with($conversation)
                ->will($this->returnValue($message));

        $objectRepository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->objectManager->expects($this->any())
                            ->method('getRepository')
                            ->with('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver')
                            ->will($this->returnValue($objectRepository));

        $objectRepository->expects($this->any())
                       ->method('persist');
        $objectRepository->expects($this->any())
                       ->method('flush')
                       ->will($this->returnValue($message));

        $this->assertInstanceOf('Eye4web\ZfcUser\Pm\Entity\MessageInterface', $this->mapper->newMessage($conversation, $messageText, $user));
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper::deleteConversations
     */
    public function testDeleteConversations()
    {
        $user = new User();
        $user->setId(1);

        $this->options->expects($this->any())
             ->method('getConversationReceiverEntity')
             ->will($this->returnValue('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver'));

        $objectRepository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->objectManager->expects($this->any())
                            ->method('getRepository')
                            ->with('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver')
                            ->will($this->returnValue($objectRepository));

        $conversationReceive = $this->getMock('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver');

        $data = [
            'conversation' => 1,
            'to' => $user->getId(),
        ];
        $objectRepository->expects($this->any())
                       ->method('findOneBy')
                       ->with($data)
                       ->will($this->returnValue($conversationReceive));

        $objectRepository->expects($this->any())
                       ->method('flush')
                       ->will($this->returnValue($conversationReceive));

        $this->mapper->deleteConversations([1], $user);
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper::isUnread
     */
    public function testIsUnread()
    {
        $conversation = new Conversation();
        $conversation->setId(1);

        $user = new User();
        $user->setId(1);

        $optionsData = ['conversation' => $conversation->getId(), 'to' => $user->getId()];

        $this->options->expects($this->once())
             ->method('getConversationReceiverEntity')
             ->will($this->returnValue('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver'));

        $objectRepository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->objectManager->expects($this->once())
                            ->method('getRepository')
                            ->with('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver')
                            ->will($this->returnValue($objectRepository));

        $conversationReceive = $this->getMock('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver');
        $objectRepository->expects($this->once())
                   ->method('findOneBy')
                   ->with($optionsData)
                   ->will($this->returnValue($conversationReceive));
        $conversationReceive->expects($this->once())
                            ->method('getUnread')
                            ->will($this->returnValue(true));

        $this->assertTrue($this->mapper->isUnread($conversation, $user));
    }

//    /**
//     * @covers Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper::newConversation
//     */
//    public function testNewConversation()
//    {
//        $data = [
//            'headline' => 'foo',
//            'message' => 'bar',
//            'to' => '2,3'
//        ];
//
//        $now = new \DateTime('now');
//
//        $user = new User;
//        $user->setId(1);
//
//        $this->options->expects($this->at(0))
//             ->method('getMessageEntity')
//             ->will($this->returnValue('Eye4web\ZfcUser\Pm\Entity\Message'));
//
//        $this->options->expects($this->at(1))
//             ->method('getConversationEntity')
//             ->will($this->returnValue('Eye4web\ZfcUser\Pm\Entity\Conversation'));
//
//        $this->options->expects($this->at(2))
//             ->method('getConversationReceiverEntity')
//             ->will($this->returnValue('Eye4web\ZfcUser\Pm\Entity\ConversationReceiver'));
//
//        $conversation =  new Conversation;
//        $conversation->setHeadline($data['headline']);
//
//        $objectRepository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
//        $this->objectManager->expects($this->any())
//                            ->method('persist')
//                            ->with($conversation);
//
//        $message =  new Message;
//        $message->setMessage($data['message']);
//        $message->setFrom($user->getId());
//        $message->setConversation($conversation);
//
//        $this->objectManager->expects($this->any())
//                            ->method('persist')
//                            ->with($message);
//
//        $receivers = explode(",", $data['to']);
//        $receivers[] = $user->getId();
//        foreach ($receivers as $receiver) {
//	    $conversationReceiver = new ConversationReceiver;
//	    $conversationReceiver->setTo($receiver);
//            $conversationReceiver->setConversation($conversation);
//
//	    $this->objectManager->expects($this->any())
//                 ->method('persist')
//                 ->with($conversationReceiver);
//	}
//
//        $this->objectManager->expects($this->any())
//                       ->method('flush');
//
//        $this->mapper->newConversation($data, $user);
//    }
}
