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

return [
    /**
     * The message entity to use
     * Default: Eye4web\ZfcUser\Pm\Entity\Message
     */
    //'messageEntity' => 'Eye4web\ZfcUser\Pm\Entity\Message',

    /**
     * The conversation entity to use
     * Default: Eye4web\ZfcUser\Pm\Entity\Conversation
     */
    //'conversationEntity' => 'Eye4web\ZfcUser\Pm\Entity\Conversation',

    /**
     * The conversation receiver entity to use
     * Default: Eye4web\ZfcUser\Pm\Entity\ConversationReceiver
     */
    //'conversationReceiverEntity' => 'Eye4web\ZfcUser\Pm\Entity\ConversationReceiver',

    /**
     * The mapper to use
     * Default: Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper
     */
    //'pmMapper' => 'Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper',

    /**
     * Sort order when reading a conversation
     * Default: DESC (newest in top)
     */
    //'messageSortOrder' => 'DESC',

    /**
     * Number of messages pr. page when reading a conversation
     * Default: 50
     */
    //'messagesPerPage' => 50,

    /**
     * Number of conversations pr. page in the list
     * Default: 20
     */
    //'conversationsPerPage' => 20,
];
