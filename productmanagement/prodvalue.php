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
//$cfg["dbhost"]="w.rdc.sae.sina.com.cn:3307"; //数据库主机名
//$cfg["dbuser"]="yo5jy4wk4o"; //数据库用户名
//$cfg["dbpass"]="xkixj1yiw5xz23100ylmmkxml0jl2yhm53h2l2lx"; //数据库密码
//$cfg["dbname"]="app_chelseapp"; //数据库名称
//$cfg["website"]="http://liushuailsg.sinaapp.com/chelsea"; //网站域名
//$cfg["webtitle"]="chelsea"; //系统名称

//引入类库及公共方法
require_once($rootDir . "lib/mysql.class.php"); //数据类
require_once($rootDir . "lib/func.class.php"); //核心类
require_once($rootDir . "ContentInfo.php");
//echo '<meta http-equiv=Content-Type content="text/html;charset=utf-8">';
//error_reporting(E_ALL & ~E_NOTICE);

?>


<?php 
$productCode = $_GET["code"];
echo 'Product code is ' . $productCode;
?>

<?php echo '</br>'; ?>

<?php
$urlTarget = 'http://www.cmbchina.com/cfweb/personal/prodvalue.aspx?PrdType=T0026&PrdCode=' . $productCode;
$pageNomboer = 2;
$urlTarget = $urlTarget . '&PageNo=' . $pageNomboer;
echo $urlTarget;
echo '</br>';

//建立Dom对象，分析HTML文件；
$htmDoc = new DOMDocument;
$htmDoc->loadHTMLFile($urlTarget);
$htmDoc->normalizeDocument();

//获得到此文档中每一个Table对象；
$tables_list = $htmDoc->getElementsByTagName('table');

foreach ($tables_list as $table)
{
    //得到Table对象的class属性
    $tableProp = $table->getAttribute('class');
    echo 'table list as ' . $tableProp . '</br>';
    if ($tableProp == 'ProductTable') {
        ParseFromDOMElement($table);
    }
    echo '---------------------------------------' . '</br>';
}

?>


<?php
    function ParseFromDOMElement(DOMElement $table) {
        echo 'ParseFromDOMElement.' . '</br>';
        
        $rows_list = $table->getElementsByTagName('tr');
        
        if ($rows_list->length === 0) {
            echo 'rows list is empty!' . '</br>';
            return;
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
            return;
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
    }
?>