<?php

namespace Eye4web\ZfcUser\Pm\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions implements ModuleOptionsInterface
{
    protected $messageEntity = 'Eye4web\ZfcUser\Pm\Entity\Message';

    protected $conversationEntity = 'Eye4web\ZfcUser\Pm\Entity\Conversation';

    protected $conversationReceiverEntity = 'Eye4web\ZfcUser\Pm\Entity\ConversationReceiver';

    protected $pmMapper = 'Eye4web\ZfcUser\Pm\Mapper\DoctrineORM\PmMapper';

    protected $messageSortOrder = 'DESC';

    /**
     * @return string
     */
    public function getMessageSortOrder()
    {
        return $this->messageSortOrder;
    }

    /**
     * @param string $messageSortOrder
     */
    public function setMessageSortOrder($messageSortOrder)
    {
        $this->messageSortOrder = $messageSortOrder;
    }

    /**
     * @return string
     */
    public function getConversationEntity()
    {
        return $this->conversationEntity;
    }

    /**
     * @param string $conversationEntity
     */
    public function setConversationEntity($conversationEntity)
    {
        $this->conversationEntity = $conversationEntity;
    }

    /**
     * @return string
     */
    public function getConversationReceiverEntity()
    {
        return $this->conversationReceiverEntity;
    }

    /**
     * @param string $conversationReceiverEntity
     */
    public function setConversationReceiverEntity($conversationReceiverEntity)
    {
        $this->conversationReceiverEntity = $conversationReceiverEntity;
    }

    /**
     * @return string
     */
    public function getMessageEntity()
    {
        return $this->messageEntity;
    }

    /**
     * @param string $messageEntity
     */
    public function setMessageEntity($messageEntity)
    {
        $this->messageEntity = $messageEntity;
    }

    /**
     * @return string
     */
    public function getPmMapper()
    {
        return $this->pmMapper;
    }

    /**
     * @param string $pmMapper
     */
    public function setPmMapper($pmMapper)
    {
        $this->pmMapper = $pmMapper;
    }
}
