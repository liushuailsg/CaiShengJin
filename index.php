<!DOCTYPE HTML><html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="content-type" content="text/html" />
	<meta name="author" content="Tuesday 2014-10-16" />
	<title>CaiShengJin</title>

<style type="text/css">
    #content table{ width: 600px;}
    #content a{color: white;}
</style>
</head>

<body>

<?php
require_once('csj.php');
?>

<center>
    <!-- form name="myForm" action="processShowData.php" -->
        <table  border="1" cellspacing="0" cellpadding="0">
            <caption>个人理财产品 -- 产品净值 </caption>
                <th valign="middle" style="width: 10%;">产品代码</th>
                <th valign="middle" style="width: 30%;">产品名称</th>
                <th valign="middle" style="width: 20%;">产品净值</th>
                <th valign="middle" style="width: 10%;">净值日期</th>
                <?php
                    foreach ($contentList as $data) {
                        if ($data->isLabel == true) {
                            continue;
                        }
                        
                ?>
                <tr align="center">
                    <td><?php echo $data->PrdCode ?></td>
                    <td><?php echo $data->PrdName ?></td>
                    <td><?php echo $data->Content ?></td>
                    <td><?php echo $data->Time ?></td>
                </tr>
                <?php
                    }
                ?>
        </table>
    <!-- /form -->
</center>


<div style="width: 600px; margin: 0 auto;" id="content">
    <img src="../xampp.gif" /><br />
    <div style="background-color:#73749A; color: white; line-height: 40px; height: 40px;  padding-left: 3px; margin-bottom: 8px;">
<a style="background-color: #73749A;" href="./phpmyadmin">phpmyadmin 账号密码: root/root </a>
    </div>
</div>
    <?php
        //phpinfo();
    ?>
</body>
</html>
