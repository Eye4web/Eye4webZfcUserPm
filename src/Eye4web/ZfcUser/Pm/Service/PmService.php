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

use Application\Entity\User;
use Eye4web\ZfcUser\Pm\Mapper\PmMapperInterface;
use Eye4web\ZfcUser\Pm\Entity\ConversationInterface;
use Eye4web\ZfcUser\Pm\Entity\MessageInterface;
use ZfcUser\Entity\UserInterface;

class PmService implements PmServiceInterface
{
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
     * @param string $userId
     * @return ConversationInterface[]
     */
    public function getUserConversations($userId)
    {
        return $this->pmMapper->getUserConversations($userId);
    }

    /**
     * @param ConversationInterface $conversation
     * @return MessageInterface[]
     */
    public function getMessages(ConversationInterface $conversation)
    {
        return $this->pmMapper->getMessages($conversation);
    }

    /**
     * @param ConversationInterface $conversation
     * @param UserInterface $user
     * @return mixed|void
     */
    public function markRead(ConversationInterface $conversation, UserInterface $user)
    {
        $this->pmMapper->markRead($conversation, $user);
    }

    /**
     * @return array|UserInterface[]
     */
    public function getUsers()
    {
        $dbUsers = $this->pmMapper->getUsers();
        $users = array();
        foreach ($dbUsers as $user) {
            $users[] = array(
                'id' => $user->getId(),
                'text' => $user->getDisplayName()
            );
        }
        return $users;
    }

    /**
     * @param string $conversationId
     * @return ConversationInterface
     */
    public function getConversation($conversationId)
    {
        return $this->pmMapper->getConversation($conversationId);
    }

    /**
     * @param ConversationInterface $conversation
     * @return UserInterface[]
     */
    public function getParticipants(ConversationInterface $conversation)
    {
        return $this->pmMapper->getParticipants($conversation);
    }

    /**
     * @param ConversationInterface $conversation
     * @param UserInterface $user
     * @return bool
     */
    public function isUnread(ConversationInterface $conversation, UserInterface $user)
    {
        return $this->pmMapper->isUnread($conversation, $user);
    }

    /**
     * @param ConversationInterface $conversation
     */
    public function markUnread(ConversationInterface $conversation)
    {
        $this->pmMapper->markUnread($conversation);
    }

    /**
     * @param array $data
     * @param UserInterface $user
     * @return ConversationInterface
     */
    public function newConversation(array $data, UserInterface $user)
    {
        $conversation = $this->pmMapper->newConversation($data, $user);

        // Mark it read for the sending user
        $this->markRead($conversation, $user);

        return $conversation;
    }

    /**
     * @param ConversationInterface $conversation
     * @param string $message
     * @param UserInterface $user
     * @return MessageInterface
     */
    public function newMessage(ConversationInterface $conversation, $message, UserInterface $user)
    {
        $message = $this->pmMapper->newMessage($conversation, $message, $user);

        $this->markUnread($conversation);
        // Mark it read for the sending user
        $this->markRead($conversation, $user);

        return $message;
    }

    /**
     * @param ConversationInterface $conversation
     * @return MessageInterface
     */
    public function getLastReply(ConversationInterface $conversation)
    {
        return $this->pmMapper->getLastReply($conversation);
    }

    /**
     * @param UserInterface $user
     * @return ConversationInterface[]
     */
    public function getUnreadConversations(UserInterface $user)
    {
        return $this->pmMapper->getUnreadConversations($user);
    }
}
