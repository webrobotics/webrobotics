<?php

class Zend_Controller_Action_Helper_Tree extends Zend_Controller_Action_Helper_Abstract
{

    function __construct() {
        $this->modelItem = new Application_Model_Item();
        $this->modelFile = new Application_Model_File();
        $this->html = "";
        $this->wrSettings = Zend_Registry::get('wrSettings');
        $this->wrFileStorage = $this->wrSettings["file"]["uri"];
    }

    function displayHtmlTree($parentId, $level) {
        $result = $this->modelItem->getChildrenList($parentId);
        foreach ($result as $item) {

            $itemFile = $this->modelFile->getFileByItem($item->item_id);
            $item->file_id = ($itemFile) ? $itemFile[0]->file_id : "";
            $item->file_path = ($itemFile) ? $itemFile[0]->file_path : "";
            $item->file_name = ($itemFile) ? $itemFile[0]->file_name : "";

            $item->level = $level;
            if ($item->item_parent_id && $item->item_type_id == 1) {
                $this->html .=
                        '<tr>
                            <td style="padding-left:'.($item->level * 20).'px;background-color:#D4D4D4;color:#000">
                                '.$item->item_name.'
                            </td>
                        </tr>';
            }
            elseif (!$item->item_parent_id) {
                $this->html .=
                    '<tr>
                            <td style="background-color:#676767;color:#fff">'.$item->item_name.'</td>
                        </tr>';
            }
            elseif ($item->item_type_id == 2 && !$item->file_id) {
                $this->html .=
                    '<tr>
                        <td style="padding-left:'.($item->level * 20).'px;background-color:#FFE8E8;color:#b94a48">
                            <strong>'.$item->item_name.'</strong> ::
                            '.$item->item_desc.'<br>
                            <span style="font-size:11px;padding:5px;">No linked files</span>
                        <div style="float:right;color:#000"><a href="#" style="color:#000">Edit</a> | <a href="#" style="color:#000">Delete</a></div>
                        </td>
                    </tr>';
            }
            elseif ($item->item_type_id == 2 && $item->file_id) {
                $this->html .=
                    '<tr>
                        <td style="padding-left:'.($item->level * 20).'px;background-color:#DFF0D8;color:#468847">
                            <strong>'.$item->item_name.'</strong> ::
                            '.$item->item_desc.'<br>
                            <span style="font-size:11px;padding:10px;">
                                <strong>Test file</strong> ->
                                <code style="color:#468847">
                                    <a id="'.$item->file_id.'" href="/'.$this->wrFileStorage.$item->file_path.$item->file_name.'">'.$item->file_name.'</a>
                                </code>
                                <code id="fileContent-'.$item->file_id.'"></code>
                            </span>
                            <script>
                                $("#'.$item->file_id.'").click(function(event){
                                    $("#fileContent-'.$item->file_id.'").append("<h1>TEst</h1>");
                                    return false;
                                });
                            </script>
                        <div style="float:right;color:#000"><a href="#" style="color:#000">Edit</a> | <a href="#" style="color:#000">Delete</a></div>
                        </td>
                    </tr>';
            }
            $this->displayHtmlTree($item->item_id, $level+1);
        }
        return $this->html;
    }

    function displayGroupSelect($parentId, $level) {
        $result = $this->modelItem->getGroupList($parentId);
        foreach ($result as $item) {
            $item->style = (!$item->item_parent_id) ? "font-weight:bold" : "";
            $this->html .= '<option style="'.$item->style.'" value="'.$item->item_id.'">'.str_repeat('&nbsp;',$level*4).$item->item_name.'</option>';
            $this->displayGroupSelect($item->item_id, $level+1);
        }
        return $this->html;
    }

    function displayGroupAndItemSelect($parentId, $level) {
        $result = $this->modelItem->getChildrenList($parentId);
        foreach ($result as $item) {
            if (!$item->item_parent_id) {
                $item->style = 'style="font-weight:bold;font-size:14px;color:#000"';
            }
            elseif($item->item_parent_id && $item->item_type_id == 1) {
                $item->style = 'style="font-weight:bold;font-size:12px;color:#003250;background-color:#E5E5E5"';
            }
            else {
                $item->style = 'style="font-size:12px;color:#595959"';
            }
            $item->disabled = ($item->item_type_id == 1) ? 'disabled' : '';
            $this->html .= '<option '.$item->style.' value="'.$item->item_id.'" '.$item->disabled.'>'.str_repeat('&nbsp;',$level*4).$item->item_name.'</option>';
            $this->displayGroupAndItemSelect($item->item_id, $level+1);
        }
        return $this->html;
    }

    function __destruct() {
        $this->modelItem = "";
    }
}