<?php

class Application_Model_Item extends Zend_Db_Table_Abstract
{	
	protected $_name = 'wr_item';
    protected $_link_name = 'wr_item_file_link';
    protected $_file = 'wr_file';
        
    public function getChildrenList($parentId)
    {
        $stmt = $this->getAdapter()->query(
            'SELECT * FROM '.$this->_name.'
                WHERE item_parent_id='.$parentId.'
                        ORDER BY item_type_id,item_id ASC;'
        );
        $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);        
        return $result;
    }

    public function getGroupList($parentId)
    {
        $stmt = $this->getAdapter()->query(
            'SELECT * FROM '.$this->_name.'
                WHERE item_parent_id='.$parentId.' and item_type_id = 1
                    ORDER BY item_id ASC;'
        );
        $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);
        return $result;
    }

	public function saveItem($itemParams)
    {
    	$itemParams["item_id"] = $this->getAdapter()->nextSequenceId('wr_item_id_seq');
    	$result = $this->getAdapter()->insert($this->_name, $itemParams);
        if ($result) {
            return $itemParams["item_id"];
        }
        else {
            return False;
        }
    }
        
    public function updateItem($itemParams)
    {
        $where = $this->getAdapter()->quoteInto('f_id = ?', $itemParams["f_id"]);
        $result = $this->update($itemParams, $where);
        return $result;          
    }
    
    public function deleteItem($fId)
    {
        $where = $this->getAdapter()->quoteInto('f_id = ?', $fId);
        $result = $this->delete($where);    
        return $result;
            
    }
    
}
