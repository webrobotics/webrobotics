<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }

    public function indexAction()
    {
        // action body
        //$this->_redirect('/online');
    }


}

