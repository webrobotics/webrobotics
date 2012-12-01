<?php

class OnlineController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->controllerName = parent::_getParam("controller");
        $this->view->controllerUrl = $this->view->baseUrl."/".$this->view->controllerName;

        $this->view->pageTitle = "Online board";
        $this->view->onlineTabActive = True;
    }

    public function indexAction()
    {
        // action body
        $this->_redirect($this->view->controllerName.'/view');
    }

    public function viewAction()
    {
        // action body
    }


}

