<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//$urlTarget = "http://www.hao123.com";
//$urlTarget = "http://www.cmbchina.com/cfweb/Personal/productdetail.aspx?code=8193&type=prodvalue";
$urlTarget = "http://www.cmbchina.com/cfweb/personal/prodvalue.aspx?PrdType=T0026&PrdCode=8193";
//$urlTarget = "prodvalue.html";

//echo '<meta http-equiv=Content-Type content="text/html;charset=utf-8">';
error_reporting(E_ALL & ~E_NOTICE);

require_once('ContentManager.php');
require_once('ProductInfo.php');

//建立Dom对象，分析HTML文件；
$htmDoc = new DOMDocument;
//$htmDoc = new DOMDocument('1.0', 'UTF-8');
//$htmDoc = new DOMDocument('1.0', 'GBK');
$htmDoc->loadHTMLFile($urlTarget);
$htmDoc->normalizeDocument();
$title=$htmDoc->getElementsByTagName('title');

echo '</br>';
echo '</br>';

//$str='hello 你好'.'</br>';
//echo $str;
//echo 'Title is '.$title->item(0)->nodeValue.'</br>';
//echo 'DOMDocument encoding is '.$htmDoc->encoding.'</br>';

//获得到此文档中每一个Table对象；
$tables_list = $htmDoc->getElementsByTagName('table');

//测试Table Count；
$tables_count = $tables_list->length;
echo '$tables_count is '.$tables_count.'</br>';

foreach ($tables_list as $table)
{
    //得到Table对象的class属性
    $tableProp = $table->getAttribute('class');
    echo $tableProp.'</br>';
    if ($tableProp == 'ProductTable')
    {
        $contentMgr = new ContentManager();
        $count = $contentMgr->ParseFromDOMElement($table);
        if ($count === 0) {
            echo $tableProp.' is empty!'.'</br>';
            continue;
        }

        //这里myParser就完成了分析动作。然后就可以进行需要的操作了。
        //比如写入MySQL。
        //$contentMgr->showContentInfo();

        echo '</br>';
        echo '</br>';

        $code='8193';
        $contentList = $contentMgr->getContentInfo();
        foreach ($contentList as $content) {
            if (empty($content)) {
                echo 'Content is empty.' . '</br>';
            } else {
                //echo $content->PrdCode.' '.$content->PrdName.' '.$content->Content.' '.$content->Time . '</br>';
//                echo '$content->PrdCode is ' . $content->PrdCode
//                    . ', type = ' . gettype($content->PrdCode)
//                    . ', length = ' . strlen($content->PrdCode) . '</br>';
//                echo strcmp($content->PrdCode, $code) . '</br>';
//                $string = $content->PrdCode;
//                echo 'start' . '</br>';
//                for($i = 0; $i < strlen($string); $i++) {
////                    $bytes[] = ord($string[$i]);
////                    echo $string[$i] . '</br>';
//                    echo $string[$i] , ' : ' , ord($string[$i]) , '</br>';
//
//                }
//                echo 'end' . '</br>';
                if (strpos($content->PrdCode, $code) != false) {
//                    echo $content->Content . '</br>';
                    $worth = floatval($content->Content);
//                    echo $worth . '</br>';
//                    break;
                    $worthArray[] = $worth;
//                    echo $worthArray. . '</br>';
//                    echo '$worthArray count is ' . count($worthArray) . '</br>';
                    
                }
            }
        }
        
        $currentWorth = floatval($contentList[1]->Content);
        echo '124234 = ' . $currentWorth . '</br>';

        $productInfo = new ProductInfo($code);
        echo $productInfo->getAssets($currentWorth) . '</br>';

    }
    echo '---------------------------------------' . '</br>';
}

$file = './YieldRate.png';
$result = @unlink ($file);
if ($result == true) {
echo 'YieldRate.png delete OK' . '</br>';
} else {
echo 'YieldRate.png delete failed' . '</br>';
}

/*<table>
<?php for($i=0; $i<count($zifuchuan); $i++){?>
<tr>
    <td><?php echo $zifuchuan[$i] ?></td>
</tr>
<?php }?>
</table>*/

/*
echo '<table>';
for ($i = 0; $i < count($worthArray); $i++) {
    echo '<tr><td>' . $i*2 . '</td><td>' . $worthArray[$i] . '</td></tr>';
}
echo '</table>';
*/


include_once('Naked.php');
//creatImageFile(array(8,4,3,2,3,3,2,1,0,7,4,3,2,3,3,5,1,0,8));
//creatImageFile($worthArray);
echo '<img src="./YieldRate.png" />';
//$path = './YieldRate.png';
//$img = imagecreatefrompng($path);
//header('Content-Type:image/png;');
//imagepng($img);

?>
