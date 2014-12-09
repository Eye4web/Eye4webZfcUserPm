<?php

namespace Eye4web\ZfcUserPmTest\Form;

use PHPUnit_Framework_TestCase;
use Eye4web\ZfcUser\Pm\Form\NewMessageForm as Form;

class NewMessageFormTest extends PHPUnit_Framework_TestCase
{
    protected $messageForm;

    public function setUp()
    {
        $this->messageForm = new Form();
    }

    public function testHasElement()
    {
        $this->assertTrue($this->messageForm->has('message'));
        $this->assertTrue($this->messageForm->has('csrf'));
        $this->assertTrue($this->messageForm->has('submit'));
    }

    public function testHasInputFilter()
    {
        $this->assertTrue($this->messageForm->getInputFilter()->has('message'));
    }
}
