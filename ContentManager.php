<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ContentParser
 *
 * @author xxxxx
 */
require_once('ContentInfo.php');
class ContentManager {
    //put your code here
    var $ContentList;
    public function __construct() {
        $this->ContentList = new ArrayObject();
    }

    public function ParseFromDOMElement(DOMElement $table)
    {
        $rows_list = $table->getElementsByTagName('tr');
        $rows_length = $rows_list->length;
        echo '$rows_length='.$rows_length.'</br>';

        foreach ($rows_list as $row)
        {
            $contentInfo = new ContentInfo();
            $contentInfo->ParseFromDOMElement($row);
            $this->ContentList->append ($contentInfo);
        }

        //test how many contents parsed.
        $count = $this->ContentList->count();
        echo 'count parsed='.$count.'</br>';
//        print_r($this->ContentList);
//        echo '</br>';
        return $count;
    }

    public function SerializeToDB()
    {
        //写入数据库，代码略。
    }

    public function showContentInfo() {
        foreach ($this->ContentList as $content) {
//            print_r($content);
//            echo '</br>';

            if (empty($content)) {
                echo 'Content is empty.' . '</br>';
            } else {
                echo $content->PrdCode.' '.$content->PrdName.' '.$content->Content.' '.$content->Time . '</br>';
            }
        }
    }

    public function getContentInfo() {
        return $this->ContentList;
    }
}

?>