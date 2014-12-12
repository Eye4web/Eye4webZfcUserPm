<?php

namespace Eye4web\ZfcUserPmTest\Entity;

use DateTime;
use Eye4web\ZfcUser\Pm\Entity\Message as Entity;
use Eye4web\ZfcUser\Pm\Entity\Conversation;
use PHPUnit_Framework_TestCase;

class MessageTest extends PHPUnit_Framework_TestCase
{
    protected $message;

    public function setUp()
    {
        $this->message = new Entity();
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Entity\Message::__construct
     * @covers Eye4web\ZfcUser\Pm\Entity\Message::__construct
     */
    public function testConstructSetIdAndDate()
    {
        $message = new Entity();
        $this->assertNotNull($message->getId());
        $this->assertInstanceOf('DateTime', $message->getDate());
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Entity\Message::setId
     * @covers Eye4web\ZfcUser\Pm\Entity\Message::getId
     */
    public function testSetGetId()
    {
        $this->message->setId(1);
        $this->assertEquals(1, $this->message->getId());
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Entity\Message::setConversation
     * @covers Eye4web\ZfcUser\Pm\Entity\Message::getConversation
     */
    public function testSetGetConversation()
    {
        $conversation = new Conversation();
        $conversation->setId(1);

        $this->message->setConversation($conversation);
        $this->assertInstanceOf('Eye4web\ZfcUser\Pm\Entity\Conversation', $this->message->getConversation());
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Entity\Message::setDate
     * @covers Eye4web\ZfcUser\Pm\Entity\Message::getDate
     */
    public function testSetGetDate()
    {
        $date = new DateTime();
        $this->message->setDate($date);
        $this->assertEquals($date, $this->message->getDate());
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Entity\Message::setFrom
     * @covers Eye4web\ZfcUser\Pm\Entity\Message::getFrom
     */
    public function testSetGetFrom()
    {
        $this->message->setFrom('045a4049-7c37-4053-97eb-7c6e8d1c1d64');
        $this->assertEquals('045a4049-7c37-4053-97eb-7c6e8d1c1d64', $this->message->getFrom());
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Entity\Message::setMessage
     * @covers Eye4web\ZfcUser\Pm\Entity\Message::getMessage
     */
    public function testSetGetMessage()
    {
        $this->message->setMessage('hello');
        $this->assertEquals('hello', $this->message->getMessage());
    }
}
