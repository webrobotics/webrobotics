<?php

class CoverageController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->controllerName = parent::_getParam("controller");
        $this->view->controllerUrl = $this->view->baseUrl."/".$this->view->controllerName;

        $this->view->pageTitle = "Product functionality groups";
        $this->view->functionalityTabActive = True;

        $this->modelSelf = new Application_Model_Item();;
    }

    public function indexAction()
    {

    }

    public function addAction()
    {
        $this->_helper->layout()->disableLayout();
    }

    public function saveAction()
    {
        $this->_helper->layout()->disableLayout();
    }
}

