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

class PmMapper implements PmMapperInterface
{
    protected $objectManager;
    protected $options;
    protected $zfcUserOptions;

    /**
     * @param ObjectManager $objectManager
     * @param ModuleOptionsInterface $options
     * @param ZfcUserModuleOptions $zfcUserOptions
     */
    public function __construct(ObjectManager $objectManager, ModuleOptionsInterface $options, ZfcUserModuleOptions $zfcUserOptions)
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
        return $this->objectManager->getRepository($repository)->findAll();
    }

    /**
     * @param ConversationInterface $conversation
     * @return array|MessageInterface[]
     */
    public function getMessages(ConversationInterface $conversation)
    {
        return $this->objectManager->getRepository($this->options->getMessageEntity())->findBy(['conversation' => $conversation->getId()], ['date' => $this->options->getMessageSortOrder()]);
    }

    /**
     * @param ConversationInterface $conversation
     * @param UserInterface $user
     * @return mixed|void
     */
    public function markRead(ConversationInterface $conversation, UserInterface $user)
    {
        $repository =  $this->objectManager->getRepository($this->options->getConversationReceiverEntity());
        $conversationReceive = $repository->findOneBy(['conversation' => $conversation->getId(), 'to' => $user->getId()]);

        $conversationReceive->setUnread(false);
        $this->objectManager->flush();
    }

    /**
     * @param ConversationInterface $conversation
     * @return array|UserInterface[]
     */
    public function getParticipants(ConversationInterface $conversation)
    {
        $receivers = $this->objectManager->getRepository($this->options->getConversationReceiverEntity())
            ->findBy(['conversation' => $conversation->getId()]);

        $participants = [];
        foreach ($receivers as $receiver) {
            $participants[] = $this->objectManager->find($this->zfcUserOptions->getUserEntityClass(), $receiver->getTo());
        }

        return $participants;
    }

    /**
     * @param string $conversationId
     * @return ConversationInterface|object
     */
    public function getConversation($conversationId)
    {
        return $this->objectManager->find($this->options->getConversationEntity(), $conversationId);
    }

    /**
     * @param string $userId
     * @return array|ConversationInterface[]
     */
    public function getUserConversations($userId)
    {
        $userReceives = $this->objectManager->getRepository($this->options->getConversationReceiverEntity())->findBy(['to' => $userId]);
        $conversations = [];
        foreach ($userReceives as $receive) {
            $conversations[] = $receive->getConversation();
        }

        return $conversations;
    }

    /**
     * @param ConversationInterface $conversation
     * @return MessageInterface
     */
    public function getLastReply(ConversationInterface $conversation)
    {
        $message = $this->objectManager->getRepository($this->options->getMessageEntity())->findOneBy(['conversation' => $conversation->getId()], ['date' => 'DESC']);
        return $message;
    }

    /**
     * @param ConversationInterface $conversation
     */
    private function markUnread(ConversationInterface $conversation)
    {
        $repository =  $this->objectManager->getRepository($this->options->getConversationReceiverEntity());
        $receivers = $repository->findBy(['conversation' => $conversation->getId()]);

        foreach ($receivers as $receiver) {
            $receiver->setUnread(true);
            $this->objectManager->persist($receiver);
        }

        $this->objectManager->flush();
    }

    /**
     * @param ConversationInterface $conversation
     * @param string $messageText
     * @param UserInterface $user
     * @return MessageInterface
     */
    public function newMessage(ConversationInterface $conversation, $messageText, UserInterface $user)
    {
        $messageEntity = $this->options->getMessageEntity();

        $message = new $messageEntity;
        $message->setMessage($messageText);
        $message->setFrom($user->getId());
        $message->setConversation($conversation);
        $this->objectManager->persist($message);

        $this->markUnread($conversation);
        // Mark it read for the sending user
        $this->markRead($conversation, $user);

        $this->objectManager->flush();

        return $message;
    }

    /**
     * @param array $data
     * @param UserInterface $user
     * @return ConversationInterface
     */
    public function newConversation(array $data, UserInterface $user)
    {
        $messageEntity = $this->options->getMessageEntity();
        $conversationEntity = $this->options->getConversationEntity();
        $conversationReceiverEntity = $this->options->getConversationReceiverEntity();

        /** @var ConversationInterface $conversation */
        $conversation = new $conversationEntity;
        $conversation->setHeadline($data['headline']);
        $this->objectManager->persist($conversation);

        $message = new $messageEntity;
        $message->setMessage($data['message']);
        $message->setFrom($user->getId());
        $message->setConversation($conversation);
        $this->objectManager->persist($message);

        $receivers = explode(",", $data['to']);
        $receivers[] = $user->getId(); // we also want the sending user to be a receiver
        foreach ($receivers as $receiver) {
            $conversationReceiver = new $conversationReceiverEntity;
            $conversationReceiver->setTo($receiver);
            $conversationReceiver->setConversation($conversation);
            $this->objectManager->persist($conversationReceiver);
        }

        $this->objectManager->flush();

        return $conversation;
    }
}
