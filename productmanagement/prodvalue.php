<?php
//====================================================
//      FileName: prodvalue.php
//      Summary:  Product net worth
//====================================================
header("Content-type: text/html; charset=utf-8");
//session_start();
//error_reporting(0);
$rootDir = '../';

//配置数据库
$cfg["dbhost"]="localhost:3306"; //数据库主机名
$cfg["dbuser"]="root"; //数据库用户名
$cfg["dbpass"]="root"; //数据库密码
$cfg["dbname"]="CaiShengJin"; //数据库名称
//$cfg["website"]="http://liushuailsg.sinaapp.com/chelsea"; //网站域名
//$cfg["webtitle"]="chelsea"; //系统名称

//引入类库及公共方法
require_once($rootDir . "lib/mysql.class.php"); //数据类
require_once($rootDir . "lib/func.class.php"); //核心类
require_once($rootDir . "ContentInfo.php");
//echo '<meta http-equiv=Content-Type content="text/html;charset=utf-8">';
//error_reporting(E_ALL & ~E_NOTICE);
$str='hello 你好'.'</br>';
echo $str;

?>


<?php 
$productCode = $_GET["code"];
echo 'Product code is ' . $productCode;
?>

<?php echo '</br>'; ?>

<?php
$urlTarget = 'http://www.cmbchina.com/cfweb/personal/prodvalue.aspx?PrdType=T0026&PrdCode=' . $productCode;
$pageNomboer = 0;
echo $urlTarget;
echo '</br>';

//$urlTarget = "../prodvalue.html";

global $db;
$del_sql = "delete from original_product_detail";
$db->query($del_sql);

do {
    echo '####################################### start ' . '</br>';
    sleep(1);
    $pageNomboer++;
    $url = $urlTarget . '&PageNo=' . $pageNomboer;
    echo $url . '</br>';
    $condition = ParseFromHTMLFile($url);
    echo 'which continue is ' . $condition . '</br>';
    echo '####################################### end ' . '</br>';
} while ($condition);

?>


<?php

    function ParseFromHTMLFile($url) {
        //建立Dom对象，分析HTML文件；
        $htmDoc = new DOMDocument;
        $htmDoc->loadHTMLFile($url);
        $htmDoc->normalizeDocument();
        
        //获得到此文档中每一个Table对象；
        $tables_list = $htmDoc->getElementsByTagName('table');
        $ret = false;
        foreach ($tables_list as $table) {
            //得到Table对象的class属性
            $tableProp = $table->getAttribute('class');
            echo 'table list as ' . $tableProp . '</br>';
            if ($tableProp == 'ProductTable') {
                $count = ParseFromDOMElement($table);
                if ($count > 1) {
                    $ret = true;
                }
            }
            echo '---------------------------------------' . '</br>';
        }
        return $ret;
    }
    
    function ParseFromDOMElement(DOMElement $table) {
        echo 'ParseFromDOMElement.' . '</br>';
        
        $rows_list = $table->getElementsByTagName('tr');
        
        if ($rows_list->length === 0) {
            echo 'rows list is empty!' . '</br>';
            return 0;
        } else {
            echo '$rows_list length is ' . $rows_list->length . '</br>';
        }
        
        $contentList = new ArrayObject();
        foreach ($rows_list as $row)
        {
            $contentInfo = new ContentInfo();
            $contentInfo->ParseFromDOMElement($row);
            $contentList->append ($contentInfo);
        }
        
        if ($contentList->count() === 0) {
            echo 'content list is empty!' . '</br>';
            return 0;
        } else {
            echo '$contentList count is ' . $contentList->count() . '</br>';
        }
        
        foreach ($contentList as $content) {
//            print_r($content);
//            echo '</br>';

            if (empty($content)) {
                echo 'Content is empty.' . '</br>';
            } else {
                echo $content->PrdCode.' '.$content->PrdName.' '.$content->Content.' '.$content->Time . '</br>';
            }
            
        }
        
        global $db;
//        $del_sql = "delete from original_product_detail";
//        $db->query($del_sql);
        
        foreach ($contentList as $content) {
            $is_label = $content->isLabel;
            $content->PrdName = str_utf8_decode($content->PrdName);
            preg_match_all("/[\x{4e00}-\x{9fa5}]+/u", $content->PrdName, $chinese) . '</br>';
            $content_name = implode("", $chinese[0]);
            if ($content->isLabel) {
                $content->PrdCode = str_utf8_decode($content->PrdCode);
                preg_match_all("/[\x{4e00}-\x{9fa5}]+/u", $content->PrdCode, $chinese) . '</br>';
                $content_code = implode("", $chinese[0]);
                $content->Content = str_utf8_decode($content->Content);
                preg_match_all("/[\x{4e00}-\x{9fa5}]+/u", $content->Content, $chinese) . '</br>';
                $content_content = implode("", $chinese[0]);
                $content->Time = str_utf8_decode($content->Time);
                preg_match_all("/[\x{4e00}-\x{9fa5}]+/u", $content->Time, $chinese) . '</br>';
                $content_time = implode("", $chinese[0]);
            } else {
                $content_code = intval($content->PrdCode);
                $content_content = strval(floatval($content->Content));
                $content_time = intval($content->Time);
                
                $insert_sql = "insert into original_product_detail (code, name, net_worth, date, is_label) values
                    ('$content_code', '$content_name', '$content_content', '$content_time', '$is_label')";
                $bet_result = $db->query($insert_sql);
                if (!$bet_result)
                {
                    $ret = "插入失败" . '</br>';
                } else {
                    $ret = "插入 ok" . '</br>';
                }
                //echo $ret;
            }
        }
        
        $eid_query = "SELECT * FROM `original_product_detail`";
        $id_result = $db->query($eid_query);
        echo '$result is ' . $id_result . '</br>';
        $eid_arr = $db->fetchAll();
        $eid_count = count($eid_arr, COUNT_NORMAL);
        echo 'count is ' . $eid_count . '</br>';
        
        return $contentList->count();
    }
?>

<?php
function str_conv_utf8($str) {
    $charset =  mb_detect_encoding($str, array('ASCII', 'GB2312', 'GBK', 'UTF-8'));
    $charset = strtolower($charset);
    echo '$charset is ' . $charset . '</br>';
    if('cp936' == $charset) {
        $charset='GBK';
    }
    if("utf-8" != $charset) {
        echo '$charset is ' . $charset . '</br>';
//        $str = iconv($charset, "UTF-8//IGNORE", $str);
        $str = iconv($charset, "UTF-8", $str);
    }
//    $text = mb_convert_encoding($str, "UTF-8", array('ASCII', 'GB2312', 'GBK', 'UTF-8'));
//    echo '$text = ' . $text . '</br>';
    return $str;
}

function str_utf8_decode($str) {
    $str = utf8_decode($str);
    //echo 'str_utf8_decode is ' . $str . '</br>';
    return $str;
}
?>

