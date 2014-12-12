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

namespace Eye4web\ZfcUser\Pm\Service;

use Eye4web\ZfcUser\Pm\Mapper\PmMapperInterface;
use Eye4web\ZfcUser\Pm\Entity\ConversationInterface;
use Eye4web\ZfcUser\Pm\Entity\ConversationReceiverInterface;
use Eye4web\ZfcUser\Pm\Entity\MessageInterface;
use Eye4web\ZfcUser\Pm\Options\ModuleOptionsInterface;
use ZfcUser\Entity\UserInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;

class PmService implements PmServiceInterface, EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    /**
     * @var PmMapperInterface
     */
    protected $pmMapper;

    /**
     * @var ModuleOptionsInterface
     */
    protected $options;

    /**
     * @var string
     */
    protected $conversationEntity;

    /**
     * @var string
     */
    protected $conversationReceiverEntity;

    /**
     * @var string
     */
    protected $messageEntity;

    /**
     * @param PmMapperInterface $pmMapper
     */
    public function __construct(PmMapperInterface $pmMapper, ModuleOptionsInterface $options)
    {
        $this->pmMapper = $pmMapper;
        $this->options = $options;
    }

    /**
     * @param  string $userId
     * @return ConversationInterface[]
     */
    public function getUserConversations($userId)
    {
        $userReceives = $this->pmMapper->getUserReceives($userId);

        $conversations = [];
        foreach ($userReceives as $receive) {
            $conversations[] = $receive->getConversation();
        }

        $this->getEventManager()->trigger(
            'getUserConversations',
            $this,
            [
            'conversations' => $conversations
            ]
        );

        return $conversations;
    }

    /**
     * @param  array         $conversationsIds
     * @param  UserInterface $user
     * @return void
     */
    public function deleteConversations(array $conversationsIds, UserInterface $user)
    {
        $this->getEventManager()->trigger(
            'deleteConversations.pre',
            $this,
            [
            'conversationsIds' => $conversationsIds,
            'user' => $user
            ]
        );

        foreach ($conversationsIds as $conversationsId) {
            $this->pmMapper->deleteConversation($conversationsId, $user);
        }


    }

    /**
     * @param  ConversationInterface $conversation
     * @return MessageInterface[]
     */
    public function getMessages(ConversationInterface $conversation)
    {
        $messages = $this->pmMapper->getMessages($conversation);

        $this->getEventManager()->trigger(
            'getMessages',
            $this,
            [
            'messages' => $messages
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
            'conversation' => $conversation,
            'user' => $user
            ]
        );

        $this->pmMapper->markRead($conversation, $user);
    }

    /**
     * @return array|UserInterface[]
     */
    public function getUsers()
    {
        $dbUsers = $this->pmMapper->getUsers();
        $users = [];
        foreach ($dbUsers as $user) {
            $users[] = [
                'id' => $user->getId(),
                'text' => $user->getDisplayName(),
            ];
        }

        $this->getEventManager()->trigger(
            'getUsers',
            $this,
            [
            'users' => $users
            ]
        );

        return $users;
    }

    /**
     * @param  string $conversationId
     * @return ConversationInterface
     */
    public function getConversation($conversationId)
    {
        $conversation = $this->pmMapper->getConversation($conversationId);

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
     * @param  ConversationInterface $conversation
     * @return UserInterface[]
     */
    public function getParticipants(ConversationInterface $conversation)
    {
        $receivers = $this->pmMapper->getReceiversByConversation($conversation);

        $participants = [];
        foreach ($receivers as $receiver) {
            $participants[] = $this->pmMapper->getUser($receiver->getTo());
        }

        $this->getEventManager()->trigger(
            'getParticipants',
            $this,
            [
            'participants' => $participants
            ]
        );

        return $participants;
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

        $isUnread = $this->pmMapper->isUnread($conversation, $user);

        $this->getEventManager()->trigger(
            'isUnread',
            $this,
            [
            'isUnread' => $isUnread
            ]
        );

        return $isUnread;
    }

    /**
     * @param ConversationInterface $conversation
     */
    public function markUnread(ConversationInterface $conversation)
    {
        $this->getEventManager()->trigger(
            'markUnread.pre',
            $this,
            [
            'conversation' => $conversation
            ]
        );

        $receivers = $this->pmMapper->getReceiversByConversation($conversation);

        foreach ($receivers as $receiver) {
            $this->pmMapper->markUnread($receiver);
        }
    }

    /**
     * @param  array         $data
     * @param  UserInterface $user
     * @return ConversationInterface
     */
    public function newConversation(array $data, UserInterface $user)
    {
        $this->getEventManager()->trigger(
            'newConversation.pre',
            $this,
            [
            'data' => $data,
            'user' => $user,
            ]
        );

        /**
         * @var ConversationInterface $conversation
         */
        $conversation = clone $this->getConversationEntity();
        // Call __construct to generate a new id
        $conversation->__construct();
        $conversation->setHeadline($data['headline']);
        $this->pmMapper->newConversation($conversation);

        $receivers = explode(",", $data['to']);
        $receivers[] = $user->getId(); // we also want the sending user to be a receiver
        foreach ($receivers as $receiver) {
            $conversationReceiver = clone $this->getConversationReceiverEntity();
            // Call __construct to generate a new id
            $conversationReceiver->__construct();
            $conversationReceiver->setTo($receiver);
            $conversationReceiver->setConversation($conversation);

            // The sending user should not have the message marked as unread
            if ($receiver == $user->getId())
            {
                $conversationReceiver->setUnread(false);
            }

            $this->pmMapper->addReceiver($conversationReceiver);
        }

        $this->newMessage($conversation, $data['message'], $user);

        $this->getEventManager()->trigger(
            'newConversation',
            $this,
            [
            'conversation' => $conversation,
            'user' => $user,
            ]
        );

        return $conversation;
    }

    /**
     * @param  ConversationInterface $conversation
     * @param  string                $messageText
     * @param  UserInterface         $user
     * @return MessageInterface
     */
    public function newMessage(ConversationInterface $conversation, $messageText, UserInterface $user)
    {
        $this->getEventManager()->trigger(
            'newMessage.pre',
            $this,
            [
            'conversation' => $conversation,
            'message' => $messageText,
            'user' => $user,
            ]
        );

        $message = clone $this->getMessageEntity();
        // Call __construct to generate a new id
        $message->__construct();
        $message->setMessage($messageText);
        $message->setFrom($user->getId());
        $message->setConversation($conversation);
        $message = $this->pmMapper->newMessage($message);

        $this->markUnread($conversation);
        // Mark it read for the sending user
        $this->markRead($conversation, $user);

        $this->getEventManager()->trigger(
            'newMessage',
            $this,
            [
            'conversation' => $conversation,
            'message' => $message,
            'user' => $user,
            ]
        );

        return $message;
    }

    /**
     * @param  ConversationInterface $conversation
     * @return MessageInterface
     */
    public function getLastReply(ConversationInterface $conversation)
    {
        $lastReply = $this->pmMapper->getLastReply($conversation);

        $this->getEventManager()->trigger(
            'getLastReply',
            $this,
            [
            'lastReply' => $lastReply,
            ]
        );

        return $lastReply;
    }

    /**
     * @param  UserInterface $user
     * @return ConversationInterface[]
     */
    public function getUnreadConversations(UserInterface $user)
    {
        $unreadConversationReceivers = $this->pmMapper->getUnreadConversationReceivers($user);

        $unreadConversations = [];
        foreach ($unreadConversationReceivers as $receive) {
            $unreadConversations[] = $receive->getConversation();
        }

        $this->getEventManager()->trigger(
            'getUnreadConversations',
            $this,
            [
            'unreadConversations' => $unreadConversations,
            ]
        );

        return $unreadConversations;
    }

    /**
     * @return ConversationInterface
     */
    public function getConversationEntity()
    {
        if (empty($this->conversationEntity)) {
            $conversationEntityClass = $this->options->getConversationEntity();
            $this->conversationEntity = new $conversationEntityClass;
        }

        return $this->conversationEntity;
    }

    /**
     * @param ConversationInterface $conversationEntity
     */
    public function setConversationEntity(ConversationInterface $conversationEntity)
    {
        $this->conversationEntity = $conversationEntity;
    }

    /**
     * @return ConversationReceiverInterface
     */
    public function getConversationReceiverEntity()
    {
        if (empty($this->conversationReceiverEntity)) {
            $conversationReceiverClass = $this->options->getConversationReceiverEntity();
            $this->conversationReceiverEntity = new $conversationReceiverClass;
        }

        return $this->conversationReceiverEntity;
    }

    /**
     * @param ConversationReceiverInterface $conversationReceiverEntity
     */
    public function setConversationReceiverEntity(ConversationReceiverInterface $conversationReceiverEntity)
    {
        $this->conversationReceiverEntity = $conversationReceiverEntity;
    }

    /**
     * @return MessageInterface
     */
    public function getMessageEntity()
    {
        if (empty($this->messageEntity)) {
            $messageEntityClass = $this->options->getMessageEntity();
            $this->messageEntity = new $messageEntityClass;
        }

        return $this->messageEntity;
    }

    /**
     * @param MessageInterface $messageEntity
     */
    public function setMessageEntity(MessageInterface $messageEntity)
    {
        $this->messageEntity = $messageEntity;
    }
}
