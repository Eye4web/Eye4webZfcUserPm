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

namespace Eye4web\ZfcUser\Pm\View\Helper;

use Eye4web\ZfcUser\Pm\Entity\ConversationInterface;
use Eye4web\ZfcUser\Pm\Entity\MessageInterface;
use Eye4web\ZfcUser\Pm\Service\PmServiceInterface;
use Zend\View\Helper\AbstractHelper;
use ZfcUser\Entity\UserInterface;
use ZfcUser\Mapper\UserInterface as ZfcUserMapperInterface;

class ZfcUserPmHelper extends AbstractHelper
{
    /** @var PmServiceInterface  */
    protected $pmService;

    /** @var ZfcUserMapperInterface  */
    protected $zfcUserMapper;

    /**
     * @param PmServiceInterface     $pmService
     * @param ZfcUserMapperInterface $zfcUserMapper
     */
    public function __construct(PmServiceInterface $pmService, ZfcUserMapperInterface $zfcUserMapper)
    {
        $this->pmService = $pmService;
        $this->zfcUserMapper = $zfcUserMapper;
    }

    /**
     * @return $this
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * @param $userId
     * @return ZfcUserMapperInterface
     */
    public function getUser($userId)
    {
        return $this->zfcUserMapper->findById($userId);
    }

    /**
     * @param  ConversationInterface $conversation
     * @return UserInterface[]
     */
    public function getParticipants(ConversationInterface $conversation)
    {
        return $this->pmService->getParticipants($conversation);
    }

    /**
     * @param  ConversationInterface $conversation
     * @return MessageInterface
     */
    public function getLastReply(ConversationInterface $conversation)
    {
        return $this->pmService->getLastReply($conversation);
    }

    /**
     * @param  ConversationInterface $conversation
     * @param  UserInterface         $user
     * @return bool
     */
    public function isUnread(ConversationInterface $conversation, UserInterface $user)
    {
        return $this->pmService->isUnread($conversation, $user);
    }

    /**
     * @param  UserInterface           $user
     * @return ConversationInterface[]
     */
    public function getUnreadConversations(UserInterface $user)
    {
        return $this->pmService->getUnreadConversations($user);
    }
}
