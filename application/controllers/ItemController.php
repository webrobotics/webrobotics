<?php

class ItemController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->controllerName = parent::_getParam("controller");
        $this->view->controllerUrl = $this->view->baseUrl."/".$this->view->controllerName;

        $this->view->pageTitle = "Product functionality";
        $this->view->functionalityTabActive = True;

        $this->modelItem = new Application_Model_Item();
        $this->modelFile = new Application_Model_File();

        $this->wrSettings = Zend_Registry::get('wrSettings');
        $this->wrFileStorage = $this->wrSettings["file"]["storage"];
        $this->wrFileStorageUri = $this->wrSettings["file"]["uri"];

    }

    public function indexAction()
    {

    }

    public function listAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->view->itemTree = $this->_helper->Tree->displayHtmlTree(0,0);
    }

    public function addAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->view->groupSelect = $this->_helper->Tree->displayGroupSelect(0,0);
    }

    function directoryToArray($directory, $recursive) {
        $arrayItems = array();
        if ($handle = opendir($directory)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if (is_dir($directory. "/" . $file)) {
                        if($recursive) {
                            $arrayItems = array_merge($arrayItems, $this->directoryToArray($directory. "/" . $file, $recursive));
                        }
                        $file = $directory . "/" . $file;
                        if (is_dir($file)) {
                            $file = preg_replace("/\/\//si", "/", $file);
                            $arrayItems[] = preg_replace("/".preg_replace("/\//si", "\/", $this->wrFileStorage)."/si", "", $file);
                        }
                    }
                }
            }
            closedir($handle);
        }
        return $arrayItems;
    }

    public function fileAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->view->groupSelect = $this->_helper->Tree->displayGroupAndItemSelect(0,0);
        $this->view->storageStructure = $this->directoryToArray($this->wrFileStorage, True);
        asort($this->view->storageStructure);
        $this->view->storagePath = $this->wrFileStorage;
    }

    public function uploadAction()
    {
        $this->_helper->layout()->disableLayout();

        $f = new Zend_Filter_Int();
        $fileParams["file_name"] = $_FILES["testFile"]["name"];
        $fileItemId = $f->filter($this->_getParam("fileItemId"));
        $fileParams["file_path"] = $this->_getParam("filePath")."/";

        # Сохраняем файл
        $adapter = new Zend_File_Transfer_Adapter_Http();
        $adapter->setDestination($this->wrFileStorage.$fileParams["file_path"]);

        # Обновляем запись в БД для файла
        $existingFile = $this->modelFile->getFileByItem($fileItemId);
        if ($existingFile) {
            Zend_Debug::dump($existingFile);
            $fileParams["file_id"] = $existingFile[0]->file_id;
            $saveResult = $this->modelFile->updateFile($fileParams);
        }
        else {
            # Создаем запись в БД
            $saveResult = $this->modelFile->saveFile($fileParams, $fileItemId);
        }
        if ($adapter->receive() && $saveResult) {
            $this->view->result = True;
            $this->view->fileId = $saveResult;
            $this->view->filePath = $fileParams["file_path"] . $fileParams["file_name"];
        }
        else {
            $this->view->result = False;
            $messages = $adapter->getMessages();
            $this->view->errorMessage = implode("\n", $messages);
        }
    }

    public function saveAction($itemParams = array())
    {
        $this->_helper->layout()->disableLayout();

        $itemParams["item_name"] = pg_escape_string($this->_getParam("itemName"));
        $itemParams["item_parent_id"] = pg_escape_string($this->_getParam("itemParentId"));
        $itemParams["item_type_id"] = pg_escape_string($this->_getParam("itemTypeId"));
        $itemParams["item_desc"] = pg_escape_string($this->_getParam("itemDesc"));

        if ($itemParams["item_name"] && $itemParams["item_parent_id"]) {
            $result = $this->modelItem->saveItem($itemParams);
            if ($result) {
                $this->view->itemId = $result;
            }
            else {
                $this->view->errorMessage = "Database error returned";
            }
        }
        else {
            $this->view->errorMessage = "Some parameters are incorrect";
        }

    }
}

