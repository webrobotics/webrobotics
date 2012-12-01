<?php

class IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }

    public function testIndexControllerOk(){
        assert(1,1,"test assert ok");
    }

    public function testIndexControllerNotOk(){
        assert(1,0,"test assert not ok");
    }


}

