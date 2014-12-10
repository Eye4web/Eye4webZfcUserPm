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

use Eye4web\ZfcUser\Pm\Entity\ConversationInterface;
use Eye4web\ZfcUser\Pm\Entity\MessageInterface;
use ZfcUser\Entity\UserInterface;

interface PmServiceInterface
{
    /**
     * @param string $userId
     * @return ConversationInterface[]
     */
    public function getUserConversations($userId);

    /**
     * @param ConversationInterface $conversation
     * @return MessageInterface[]
     */
    public function getMessages(ConversationInterface $conversation);

    /**
     * @param ConversationInterface $conversation
     * @param UserInterface $user
     * @return mixed
     */
    public function markRead(ConversationInterface $conversation, UserInterface $user);

    /**
     * @param array $conversationsIds
     * @param UserInterface $user
     * @return void
     */
    public function deleteConversations(array $conversationsIds, UserInterface $user);

    /**
     * @return UserInterface[]
     */
    public function getUsers();

    /**
     * @param ConversationInterface $conversation
     * @param UserInterface $user
     * return bool
     */
    public function isUnread(ConversationInterface $conversation, UserInterface $user);

    /**
     * @param string $conversationId
     * @return ConversationInterface
     */
    public function getConversation($conversationId);

    /**
     * @param ConversationInterface $conversation
     * @return UserInterface[]
     */
    public function getParticipants(ConversationInterface $conversation);

    /**
     * @param array $data
     * @param UserInterface $user
     * @return ConversationInterface
     */
    public function newConversation(array $data, UserInterface $user);

    /**
     * @param ConversationInterface $conversation
     * @param string $message
     * @param UserInterface $user
     * @return MessageInterface
     */
    public function newMessage(ConversationInterface$conversation, $message, UserInterface $user);

    /**
     * @param ConversationInterface $conversation
     * @return MessageInterface
     */
    public function getLastReply(ConversationInterface $conversation);

    /**
     * @param ConversationInterface $conversation
     */
    public function markUnread(ConversationInterface $conversation);

    /**
     * @param UserInterface $user
     * @return ConversationInterface[]
     */
    public function getUnreadConversations(UserInterface $user);
}
