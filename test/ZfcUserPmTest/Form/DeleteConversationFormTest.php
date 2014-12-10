<?php

namespace Eye4web\ZfcUserPmTest\Form;

use PHPUnit_Framework_TestCase;
use Eye4web\ZfcUser\Pm\Form\DeleteConversationsForm as Form;

class DeleteConversationsFormTest extends PHPUnit_Framework_TestCase
{
    protected $deleteConversationForm;

    public function setUp()
    {
        $this->deleteConversationForm = new Form();
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Form\DeleteConversationsForm::__construct
     * @covers Eye4web\ZfcUser\Pm\Form\DeleteConversationsForm::__construct
     */
    public function testHasElement()
    {
        $this->assertTrue($this->deleteConversationForm->has('collectionIds[]'));
        $this->assertTrue($this->deleteConversationForm->has('csrf'));
        $this->assertTrue($this->deleteConversationForm->has('submit'));
    }

    /**
     * @covers Eye4web\ZfcUser\Pm\Form\DeleteConversationsForm::getInputFilterSpecification
     */
    public function testHasInputFilter()
    {
        $this->assertTrue($this->deleteConversationForm->getInputFilter()->has('collectionIds[]'));
    }
}
