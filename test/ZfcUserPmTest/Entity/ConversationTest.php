<?php

namespace Eye4web\ZfcUserPmTest\Entity;

use DateTime;
use PHPUnit_Framework_TestCase;
use Eye4web\ZfcUser\Pm\Entity\Conversation as Entity;

class ConversationTest extends PHPUnit_Framework_TestCase
{
    protected $conversation;

    public function setUp()
    {
        $this->conversation = new Entity();
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Entity\Conversation::__construct
     * @covers Eye4web\ZfcUser\Pm\Entity\Conversation::__construct
     */
    public function testConstructSetIdAndDate()
    {
        $conversation = new Entity();
        $this->assertNotNull($conversation->getId());
        $this->assertInstanceOf('DateTime', $conversation->getDate());
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Entity\Conversation::setId
     * @covers Eye4web\ZfcUser\Pm\Entity\Conversation::getId
     */
    public function testSetGetId()
    {
        $this->conversation->setId(1);
        $this->assertEquals(1, $this->conversation->getId());
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Entity\Conversation::setHeadline
     * @covers Eye4web\ZfcUser\Pm\Entity\Conversation::getHeadline
     */
    public function testSetGetHeadLine()
    {
        $this->conversation->setHeadline('foo');
        $this->assertEquals('foo', $this->conversation->getHeadline());
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Entity\Conversation::setDate
     * @covers Eye4web\ZfcUser\Pm\Entity\Conversation::getDate
     */
    public function testSetGetDate()
    {
        $date = new DateTime();
        $this->conversation->setDate($date);
        $this->assertEquals($date, $this->conversation->getDate());
    }
}
