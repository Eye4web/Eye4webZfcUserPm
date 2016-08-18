<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace Eye4web\ZfcUser\Pm\Mapper\DoctrineORM;

use Doctrine\Common\Persistence\ObjectManager;
use Eye4web\ZfcUser\Pm\Entity\ConversationInterface;
use Eye4web\ZfcUser\Pm\Entity\ConversationReceiverInterface;
use Eye4web\ZfcUser\Pm\Mapper\PmMapperInterface;
use Eye4web\ZfcUser\Pm\Entity\MessageInterface;
use Eye4web\ZfcUser\Pm\Options\ModuleOptionsInterface;
use ZfcUser\Entity\UserInterface;
use ZfcUser\Options\ModuleOptions as ZfcUserModuleOptions;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;

class PmMapper implements PmMapperInterface, EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var ModuleOptionsInterface
     */
    protected $options;

    /**
     * @var ZfcUserModuleOptions
     */
    protected $zfcUserOptions;

    /**
     * @param ObjectManager          $objectManager
     * @param ModuleOptionsInterface $options
     * @param ZfcUserModuleOptions   $zfcUserOptions
     */
    public function __construct(
        ObjectManager $objectManager,
        ModuleOptionsInterface $options,
        ZfcUserModuleOptions $zfcUserOptions
    ) {
        $this->objectManager = $objectManager;
        $this->options = $options;
        $this->zfcUserOptions = $zfcUserOptions;
    }

    /**
     * @return array|UserInterface[]
     */
    public function getUsers()
    {
        $repository = $this->zfcUserOptions->getUserEntityClass();
        $users = $this->objectManager->getRepository($repository)->findAll();

        $this->getEventManager()->trigger(
            'getUsers',
            $this,
            [
            'users' => $users,
            ]
        );

        return $users;
    }

    /**
     * @param  UserInterface $user
     * @return ConversationReceiverInterface[]
     */
    public function getUnreadConversationReceivers(UserInterface $user)
    {
        $findBy = [
            'to' => $user->getId(),
            'unread' => true,
            'deleted' => false
        ];
        $findByObject = (object) $findBy;

        $this->getEventManager()->trigger(
            'getUnreadConversationReceivers.pre',
            $this,
            [
                'user' => $user,
                'findBy' => $findByObject,
            ]
        );

        $conversationReceivers = $this->objectManager->getRepository($this->options->getConversationReceiverEntity())->findBy((array) $findByObject);

        $this->getEventManager()->trigger(
            'getUnreadConversationReceivers',
            $this,
            [
                'user' => $user,
                'conversationReceivers' => $conversationReceivers
            ]
        );

        return $conversationReceivers;
    }

    /**
     * @param  ConversationInterface $conversation
     * @return array|MessageInterface[]
     */
    public function getMessages(ConversationInterface $conversation)
    {
        $this->getEventManager()->trigger(
            'getMessages.pre',
            $this,
            [
            'conversation' => $conversation
            ]
        );

        $messages = $this->objectManager->getRepository($this->options->getMessageEntity())->findBy(
            [
            'conversation' => $conversation->getId(),
            ],
            [
            'date' => $this->options->getMessageSortOrder()
            ]
        );

        $this->getEventManager()->trigger(
            'getMessages',
            $this,
            [
            'messages' => $messages,
            'conversation' => $conversation
            ]
        );

        return $messages;
    }

    /**
     * @param  ConversationInterface $conversation
     * @param  UserInterface         $user
     * @return mixed|void
     */
    public function markRead(ConversationInterface $conversation, UserInterface $user)
    {
        $this->getEventManager()->trigger(
            'markRead.pre',
            $this,
            [
                'user' => $user,
                'conversation' => $conversation
            ]
        );

        $repository =  $this->objectManager->getRepository($this->options->getConversationReceiverEntity());
        $conversationReceive = $repository->findOneBy(['conversation' => $conversation->getId(), 'to' => $user->getId()]);

        $conversationReceive->setUnread(false);
        $this->objectManager->flush();
    }

    /**
     * @param string $userId
     * @return UserInterface
     */
    public function getUser($userId)
    {
        return $this->objectManager->find($this->zfcUserOptions->getUserEntityClass(), $userId);
    }

    /**
     * @param  ConversationInterface $conversation
     * @return array|ConversationReceiverInterface[]
     */
    public function getReceiversByConversation(ConversationInterface $conversation)
    {
        $this->getEventManager()->trigger(
            'getReceiversByConversation.pre',
            $this,
            [
                'conversation' => $conversation
            ]
        );

        $receivers = $this->objectManager->getRepository($this->options->getConversationReceiverEntity())
            ->findBy(['conversation' => $conversation->getId()]);

        $this->getEventManager()->trigger(
            'getReceiversByConversation',
            $this,
            [
                'conversation' => $conversation,
                'receivers' => $receivers,
            ]
        );

        return $receivers;
    }

    /**
     * @param  string $conversationId
     * @return ConversationInterface|object
     */
    public function getConversation($conversationId)
    {
        $conversation = $this->objectManager->find($this->options->getConversationEntity(), $conversationId);

        $this->getEventManager()->trigger(
            'getConversation',
            $this,
            [
            'conversation' => $conversation
            ]
        );

        return $conversation;
    }

    /**
     * @param  string $userId
     * @return array|ConversationReceiverInterface[]
     */
    public function getUserReceives($userId)
    {
        $queryBuilder = $this->objectManager->createQueryBuilder();
        $queryBuilder->select('r')
            ->from($this->options->getConversationReceiverEntity(), 'r')
            ->from($this->options->getMessageEntity(), 'm')
            ->leftJoin('r.conversation', 'c')
            ->where('r.to = :to')
            ->andWhere('m.conversation = c')
            ->andWhere('r.deleted = false')
            ->orderBy('m.date', 'DESC');

        $queryBuilder->setParameter('to', $userId);

        $this->getEventManager()->trigger(
            'getUserConversations.pre',
            $this,
            [
                'queryBuilder' => $queryBuilder
            ]
        );
        
        $userReceives = $queryBuilder->getQuery()->getResult();

        $this->getEventManager()->trigger(
            'getUserConversations',
            $this,
            [
            'receives' => $userReceives
            ]
        );

        return $userReceives;
    }

    /**
     * @param  ConversationInterface $conversation
     * @return MessageInterface
     */
    public function getLastReply(ConversationInterface $conversation)
    {
        $this->getEventManager()->trigger(
            'getLastReply.pre',
            $this,
            [
            'conversation' => $conversation
            ]
        );

        $message = $this->objectManager->getRepository($this->options->getMessageEntity())->findOneBy(['conversation' => $conversation->getId()], ['date' => 'DESC']);

        $this->getEventManager()->trigger(
            'getLastReply.pre',
            $this,
            [
            'conversation' => $conversation,
            'message' => $message
            ]
        );

        return $message;
    }

    /**
     * @param ConversationReceiverInterface $conversationRecevier
     */
    public function markUnread(ConversationReceiverInterface $conversationRecevier)
    {
        $this->getEventManager()->trigger(
            'markUnread.pre',
            $this,
            [
            'receiver' => $conversationRecevier,
            ]
        );

        $conversationRecevier->setUnread(true);

        $this->objectManager->flush();
    }

    /**
     * @param  MessageInterface $message
     * @return MessageInterface
     */
    public function newMessage(MessageInterface $message)
    {
        $this->getEventManager()->trigger(
            'newMessage.pre',
            $this,
            [
            'message' => $message
            ]
        );

        $this->objectManager->persist($message);
        $this->objectManager->flush();

        $this->getEventManager()->trigger(
            'newMessage',
            $this,
            [
            'message' => $message
            ]
        );

        return $message;
    }

    /**
     * @param  string         $conversationsIds
     * @param  UserInterface $user
     * @return void
     */
    public function deleteConversation($conversationsId, UserInterface $user)
    {
        $this->getEventManager()->trigger(
            'deleteConversations.pre',
            $this,
            [
            'conversationsId' => $conversationsId,
            'user' => $user
            ]
        );

        $repository = $this->objectManager->getRepository($this->options->getConversationReceiverEntity());
        $conversationReceiver = $repository->findOneBy(['conversation' => $conversationsId, 'to' => $user->getId()]);
        $conversationReceiver->setDeleted(true);

        $this->objectManager->flush();
    }

    /**
     * @param  ConversationInterface $conversation
     * @param  UserInterface         $user
     * @return bool
     */
    public function isUnread(ConversationInterface $conversation, UserInterface $user)
    {
        $this->getEventManager()->trigger(
            'isUnread.pre',
            $this,
            [
            'conversation' => $conversation,
            'user' => $user
            ]
        );

        $repository = $this->objectManager->getRepository($this->options->getConversationReceiverEntity());
        $conversationReceiver = $repository->findOneBy(['conversation' => $conversation->getId(), 'to' => $user->getId()]);

        $unread = $conversationReceiver->getUnread();

        $this->getEventManager()->trigger(
            'isUnread',
            $this,
            [
            'conversation' => $conversation,
            'unread' => $unread
            ]
        );

        return $unread;
    }

    /**
     * @param  ConversationInterface $conversation
     * @return ConversationInterface
     */
    public function newConversation(ConversationInterface $conversation)
    {
        $this->getEventManager()->trigger(
            'newConversation.pre',
            $this,
            [
            'conversation' => $conversation
            ]
        );

        $this->objectManager->persist($conversation);
        $this->objectManager->flush();

        $this->getEventManager()->trigger(
            'newConversation',
            $this,
            [
            'conversation' => $conversation
            ]
        );

        return $conversation;
    }

    /**
     * @param ConversationReceiverInterface $receiver
     * @return ConversationReceiverInterface
     */
    public function addReceiver(ConversationReceiverInterface $receiver)
    {
        $this->getEventManager()->trigger(
            'addReceiver.pre',
            $this,
            [
                'receiver' => $receiver
            ]
        );

        $this->objectManager->persist($receiver);
        $this->objectManager->flush();

        $this->getEventManager()->trigger(
            'addReceiver',
            $this,
            [
                'conversation' => $receiver
            ]
        );

        return $receiver;
    }
}
