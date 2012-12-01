<?php

class Application_Model_File extends Zend_Db_Table_Abstract
{
    protected $_name = 'wr_file';
    protected $_link_name = 'wr_item_file_link';
    protected $_item = 'wr_item';

    public function getFileByItem($itemId)
    {
        $stmt = $this->getAdapter()->query(
            'SELECT a.* FROM '.$this->_name.' a, '.$this->_link_name.' b
                WHERE b.ifl_item_id='.$itemId.'
                    AND b.ifl_file_id = a.file_id;'
        );
        $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);
        return $result;
    }

	public function saveFile($fileParams, $fileItemId)
    {
    	$fileParams["file_id"] = $this->getAdapter()->nextSequenceId('wr_file_id_seq');
    	$result = $this->getAdapter()->insert($this->_name, $fileParams);
        if ($result) {
            $iflParams["ifl_id"] = $this->getAdapter()->nextSequenceId('wr_ifl_id_seq');
            $iflParams["ifl_item_id"] = $fileItemId;
            $iflParams["ifl_file_id"] = $fileParams["file_id"];
            $result = $this->getAdapter()->insert($this->_link_name, $iflParams);
            if ($result) {
                return $fileParams["file_id"];
            }
            else {
                return False;
            }
        }
        else {
            return False;
        }
    }
        
    public function updateFile($itemParams)
    {
        $where = $this->getAdapter()->quoteInto('file_id = ?', $itemParams["file_id"]);
        $result = $this->update($itemParams, $where);
        return $result;          
    }
    
    public function deleteFile($fId)
    {
        $where = $this->getAdapter()->quoteInto('f_id = ?', $fId);
        $result = $this->delete($where);    
        return $result;
            
    }
    
}
