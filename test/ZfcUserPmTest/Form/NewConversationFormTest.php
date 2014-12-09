<?php

namespace Eye4web\ZfcUserPmTest\Form;

use PHPUnit_Framework_TestCase;
use Eye4web\ZfcUser\Pm\Form\NewConversationForm as Form;

class NewConversationFormTest extends PHPUnit_Framework_TestCase
{
    protected $messageForm;

    public function setUp()
    {
        $this->messageForm = new Form();
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Form\NewConversationForm::__construct
     * @covers Eye4web\ZfcUser\Pm\Form\NewConversationForm::__construct
     */
    public function testHasElement()
    {
        $this->assertTrue($this->messageForm->has('headline'));
        $this->assertTrue($this->messageForm->has('message'));
        $this->assertTrue($this->messageForm->has('to'));
        $this->assertTrue($this->messageForm->has('csrf'));
        $this->assertTrue($this->messageForm->has('submit'));
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Form\NewConversationForm::has
     * @covers Eye4web\ZfcUser\Pm\Form\NewConversationForm::has
     */
    public function testHasInputFilter()
    {
        $this->assertTrue($this->messageForm->getInputFilter()->has('headline'));
        $this->assertTrue($this->messageForm->getInputFilter()->has('message'));
        $this->assertTrue($this->messageForm->getInputFilter()->has('to'));
    }
}
