<?php

namespace Eye4web\ZfcUserPmTest\Controller;

use Eye4web\ZfcUser\Pm\Controller\PmController;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class PmControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include dirname(dirname(__DIR__)) .'/TestConfig.php'
        );

        parent::setUp();

    }
    
    public function testAccessPmIndexAction()
    {

    }
}