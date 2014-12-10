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
    public function __construct(ObjectManager $objectManager,
                                ModuleOptionsInterface $options,
                                ZfcUserModuleOptions $zfcUserOptions)
    {
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

        $this->getEventManager()->trigger('getUsers', $this, [
            'users' => $users,
        ]);

        return $users;
    }

    /**
     * @param  UserInterface           $user
     * @return ConversationInterface[]
     */
    public function getUnreadConversations(UserInterface $user)
    {
        $this->getEventManager()->trigger('getUnreadConversations.pre', $this, [
            'user' => $user,
        ]);

        $userReceives = $this->objectManager->getRepository($this->options->getConversationReceiverEntity())->findBy(['to' => $user->getId(), 'unread' => true, 'deleted' => false]);
        $conversations = [];
        foreach ($userReceives as $receive) {
            $conversations[] = $receive->getConversation();
        }

        $this->getEventManager()->trigger('getUnreadConversations', $this, [
            'user' => $user,
            'conversations' => $conversations
        ]);

        return $conversations;
    }

    /**
     * @param  ConversationInterface    $conversation
     * @return array|MessageInterface[]
     */
    public function getMessages(ConversationInterface $conversation)
    {
        $this->getEventManager()->trigger('getMessages.pre', $this, [
            'conversation' => $conversation
        ]);

        $messages = $this->objectManager->getRepository($this->options->getMessageEntity())->findBy([
            'conversation' => $conversation->getId(),
        ], [
            'date' => $this->options->getMessageSortOrder()
        ]);

        $this->getEventManager()->trigger('getMessages', $this, [
            'messages' => $messages,
            'conversation' => $conversation
        ]);

        return $messages;
    }

    /**
     * @param  ConversationInterface $conversation
     * @param  UserInterface         $user
     * @return mixed|void
     */
    public function markRead(ConversationInterface $conversation, UserInterface $user)
    {
        $this->getEventManager()->trigger('markRead.pre', $this, [
            'user' => $user,
            'conversation' => $conversation
        ]);

        $repository =  $this->objectManager->getRepository($this->options->getConversationReceiverEntity());
        $conversationReceive = $repository->findOneBy(['conversation' => $conversation->getId(), 'to' => $user->getId()]);

        $conversationReceive->setUnread(false);
        $this->objectManager->flush();
    }

    /**
     * @param  ConversationInterface $conversation
     * @return array|UserInterface[]
     */
    public function getParticipants(ConversationInterface $conversation)
    {
        $this->getEventManager()->trigger('getParticipants.pre', $this, [
            'conversation' => $conversation
        ]);

        $receivers = $this->objectManager->getRepository($this->options->getConversationReceiverEntity())
            ->findBy(['conversation' => $conversation->getId()]);

        $participants = [];
        foreach ($receivers as $receiver) {
            $participants[] = $this->objectManager->find($this->zfcUserOptions->getUserEntityClass(), $receiver->getTo());
        }

        $this->getEventManager()->trigger('getParticipants', $this, [
            'conversation' => $conversation,
            'participants' => $participants,
        ]);

        return $participants;
    }

    /**
     * @param  string                       $conversationId
     * @return ConversationInterface|object
     */
    public function getConversation($conversationId)
    {
        $conversation = $this->objectManager->find($this->options->getConversationEntity(), $conversationId);

        $this->getEventManager()->trigger('getConversation', $this, [
            'conversation' => $conversation
        ]);

        return $conversation;
    }

    /**
     * @param  string                        $userId
     * @return array|ConversationInterface[]
     */
    public function getUserConversations($userId)
    {
        $userReceives = $this->objectManager->getRepository($this->options->getConversationReceiverEntity())->findBy(['to' => $userId, 'deleted' => false]);
        $conversations = [];
        foreach ($userReceives as $receive) {
            $conversations[] = $receive->getConversation();
        }

        $this->getEventManager()->trigger('getUserConversations', $this, [
            'conversations' => $conversations
        ]);

        return $conversations;
    }

    /**
     * @param  ConversationInterface $conversation
     * @return MessageInterface
     */
    public function getLastReply(ConversationInterface $conversation)
    {
        $this->getEventManager()->trigger('getLastReply.pre', $this, [
            'conversation' => $conversation
        ]);

        $message = $this->objectManager->getRepository($this->options->getMessageEntity())->findOneBy(['conversation' => $conversation->getId()], ['date' => 'DESC']);

        $this->getEventManager()->trigger('getLastReply.pre', $this, [
            'conversation' => $conversation,
            'message' => $message
        ]);

        return $message;
    }

    /**
     * @param ConversationInterface $conversation
     */
    public function markUnread(ConversationInterface $conversation)
    {
        $this->getEventManager()->trigger('markUnread.pre', $this, [
            'conversation' => $conversation,
        ]);

        $repository =  $this->objectManager->getRepository($this->options->getConversationReceiverEntity());
        $receivers = $repository->findBy(['conversation' => $conversation->getId()]);

        foreach ($receivers as $receiver) {
            $receiver->setUnread(true);
            $this->objectManager->persist($receiver);
        }

        $this->objectManager->flush();
    }

    /**
     * @param  ConversationInterface $conversation
     * @param  string                $messageText
     * @param  UserInterface         $user
     * @return MessageInterface
     */
    public function newMessage(ConversationInterface $conversation, $messageText, UserInterface $user)
    {
        $this->getEventManager()->trigger('newMessage.pre', $this, [
            'conversation' => $conversation,
            'message' => $messageText,
            'user' => $user
        ]);

        $messageEntity = $this->options->getMessageEntity();

        $message = new $messageEntity();
        $message->setMessage($messageText);
        $message->setFrom($user->getId());
        $message->setConversation($conversation);
        $this->objectManager->persist($message);

        $this->objectManager->flush();

        $this->getEventManager()->trigger('newMessage', $this, [
            'conversation' => $conversation,
            'message' => $message,
            'user' => $user
        ]);

        return $message;
    }

    /**
     * @param  array         $conversationsIds
     * @param  UserInterface $user
     * @return void
     */
    public function deleteConversations(array $conversationsIds, UserInterface $user)
    {
        $this->getEventManager()->trigger('deleteConversations.pre', $this, [
            'conversationsIds' => $conversationsIds,
            'user' => $user
        ]);

        $repository = $this->objectManager->getRepository($this->options->getConversationReceiverEntity());

        foreach ($conversationsIds as $conversationsId) {
            $conversationReceiver = $repository->findOneBy(['conversation' => $conversationsId, 'to' => $user->getId()]);
            $conversationReceiver->setDeleted(true);
        }

        $this->objectManager->flush();
    }

    /**
     * @param  ConversationInterface $conversation
     * @param  UserInterface         $user
     * @return bool
     */
    public function isUnread(ConversationInterface $conversation, UserInterface $user)
    {
        $this->getEventManager()->trigger('isUnread.pre', $this, [
            'conversation' => $conversation,
            'user' => $user
        ]);

        $repository = $this->objectManager->getRepository($this->options->getConversationReceiverEntity());
        $conversationReceiver = $repository->findOneBy(['conversation' => $conversation->getId(), 'to' => $user->getId()]);

        $unread = $conversationReceiver->getUnread();

        $this->getEventManager()->trigger('isUnread', $this, [
            'conversation' => $conversation,
            'unread' => $unread
        ]);

        return $unread;
    }

    /**
     * @param  array                 $data
     * @param  UserInterface         $user
     * @return ConversationInterface
     */
    public function newConversation(array $data, UserInterface $user)
    {
        $this->getEventManager()->trigger('newConversation.pre', $this, [
            'data' => $data,
            'user' => $user
        ]);

        $messageEntity = $this->options->getMessageEntity();
        $conversationEntity = $this->options->getConversationEntity();
        $conversationReceiverEntity = $this->options->getConversationReceiverEntity();

        /** @var ConversationInterface $conversation */
        $conversation = new $conversationEntity();
        $conversation->setHeadline($data['headline']);
        $this->objectManager->persist($conversation);

        $message = new $messageEntity();
        $message->setMessage($data['message']);
        $message->setFrom($user->getId());
        $message->setConversation($conversation);
        $this->objectManager->persist($message);

        $receivers = explode(",", $data['to']);
        $receivers[] = $user->getId(); // we also want the sending user to be a receiver
        $conversationReceivers = [];
        foreach ($receivers as $receiver) {
            $conversationReceiver = new $conversationReceiverEntity();
            $conversationReceiver->setTo($receiver);
            $conversationReceiver->setConversation($conversation);
            $this->objectManager->persist($conversationReceiver);

            $conversationReceivers[] = $conversationReceiver;
        }

        $this->objectManager->flush();

        $this->getEventManager()->trigger('newConversation.pre', $this, [
            'conversationReceivers' => $conversationReceivers,
            'message' => $message,
            'conversation' => $conversation,
            'user' => $user
        ]);

        return $conversation;
    }
}
