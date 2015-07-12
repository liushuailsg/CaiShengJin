<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ContentInfo
 *
 * @author xxxxx
 */
class ContentInfo {

    var $isLabel;

    var $PrdCode;
    var $PrdName;
    var $Content;
    var $Time;

    public function ParseFromDOMElement(DOMElement $row)
    {
        $label_list = $row->getElementsByTagName('th');
        $cells_list = $row->getElementsByTagName('td');

        if ($label_list->length > 0) {
            $list = $label_list;
            $this->isLabel = true;
        } else if ($cells_list->length > 0) {
            $list = $cells_list;
            $this->isLabel = false;
        } else {
            Log::debug('no table');
            return;
        }
//        Log::debug('$list='.$list->length);

        $curIndex = 0;
        foreach ($list as $item)
        {
            switch ($curIndex++)
            {
                case 0:
                    $this->PrdCode = $item->nodeValue;
                    break;
                case 1:
                    $this->PrdName = $item->nodeValue;
                    break;
                case 2:
                    $this->Content = $item->nodeValue;
                    break;
                case 3:
                    $this->Time = $item->nodeValue;
                    break;
            }
        }
    }
}

?>
