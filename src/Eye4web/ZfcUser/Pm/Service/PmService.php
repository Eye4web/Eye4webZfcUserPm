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
use Eye4web\ZfcUser\Pm\Entity\MessageInterface;
use ZfcUser\Entity\UserInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;

class PmService implements PmServiceInterface, EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    /** @var PmMapperInterface */
    protected $pmMapper;

    /**
     * @param PmMapperInterface $pmMapper
     */
    public function __construct(PmMapperInterface $pmMapper)
    {
        $this->pmMapper = $pmMapper;
    }

    /**
     * @param  string                  $userId
     * @return ConversationInterface[]
     */
    public function getUserConversations($userId)
    {
        $conversations = $this->pmMapper->getUserConversations($userId);

        $this->getEventManager()->trigger('getUserConversations', $this, [
            'conversations' => $conversations
        ]);

        return $conversations;
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

        $this->pmMapper->deleteConversations($conversationsIds, $user);
    }

    /**
     * @param  ConversationInterface $conversation
     * @return MessageInterface[]
     */
    public function getMessages(ConversationInterface $conversation)
    {
        $messages = $this->pmMapper->getMessages($conversation);

        $this->getEventManager()->trigger('getMessages', $this, [
            'messages' => $messages
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
            'conversation' => $conversation,
            'user' => $user
        ]);

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

        $this->getEventManager()->trigger('getUsers', $this, [
            'users' => $users
        ]);

        return $users;
    }

    /**
     * @param  string                $conversationId
     * @return ConversationInterface
     */
    public function getConversation($conversationId)
    {
        $conversation = $this->pmMapper->getConversation($conversationId);

        $this->getEventManager()->trigger('getConversation', $this, [
            'conversation' => $conversation
        ]);

        return $conversation;
    }

    /**
     * @param  ConversationInterface $conversation
     * @return UserInterface[]
     */
    public function getParticipants(ConversationInterface $conversation)
    {
        $participants = $this->pmMapper->getParticipants($conversation);

        $this->getEventManager()->trigger('getParticipants', $this, [
            'participants' => $participants
        ]);

        return $participants;
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

        $isUnread = $this->pmMapper->isUnread($conversation, $user);

        $this->getEventManager()->trigger('isUnread', $this, [
            'isUnread' => $isUnread
        ]);

        return $isUnread;
    }

    /**
     * @param ConversationInterface $conversation
     */
    public function markUnread(ConversationInterface $conversation)
    {
        $this->getEventManager()->trigger('markUnread.pre', $this, [
            'conversation' => $conversation
        ]);

        $this->pmMapper->markUnread($conversation);
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
            'user' => $user,
        ]);

        $conversation = $this->pmMapper->newConversation($data, $user);

        // Mark it read for the sending user
        $this->markRead($conversation, $user);

        $this->getEventManager()->trigger('newConversation', $this, [
            'conversation' => $conversation,
            'user' => $user,
        ]);

        return $conversation;
    }

    /**
     * @param  ConversationInterface $conversation
     * @param  string                $message
     * @param  UserInterface         $user
     * @return MessageInterface
     */
    public function newMessage(ConversationInterface $conversation, $message, UserInterface $user)
    {
        $this->getEventManager()->trigger('newMessage.pre', $this, [
            'conversation' => $conversation,
            'message' => $message,
            'user' => $user,
        ]);

        $message = $this->pmMapper->newMessage($conversation, $message, $user);

        $this->markUnread($conversation);
        // Mark it read for the sending user
        $this->markRead($conversation, $user);

        $this->getEventManager()->trigger('newMessage', $this, [
            'conversation' => $conversation,
            'message' => $message,
            'user' => $user,
        ]);

        return $message;
    }

    /**
     * @param  ConversationInterface $conversation
     * @return MessageInterface
     */
    public function getLastReply(ConversationInterface $conversation)
    {
        $lastReply = $this->pmMapper->getLastReply($conversation);

        $this->getEventManager()->trigger('getLastReply', $this, [
            'lastReply' => $lastReply,
        ]);

        return $lastReply;
    }

    /**
     * @param  UserInterface           $user
     * @return ConversationInterface[]
     */
    public function getUnreadConversations(UserInterface $user)
    {
        $unreadConversations = $this->pmMapper->getUnreadConversations($user);

        $this->getEventManager()->trigger('getUnreadConversations', $this, [
            'unreadConversations' => $unreadConversations,
        ]);

        return $unreadConversations;
    }
}
