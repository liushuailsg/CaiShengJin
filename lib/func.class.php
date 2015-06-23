<?php
//====================================================
//		FileName: func.class.php
//		Summary:  系统函数配置
//====================================================

//当前时区
date_default_timezone_set('asia/shanghai');

//初始化数据库连接
$db = new mysql($cfg["dbhost"],$cfg["dbuser"],$cfg["dbpass"],$cfg["dbname"]);

function check_input($value)
{
    // 去除斜杠
    if (get_magic_quotes_gpc())
    {
        $value = stripslashes($value);
    }
    // 如果不是数字则加引号
    if (!is_numeric($value))
    {
        $value = "'" . mysql_real_escape_string($value) . "'";
    }
    return $value;
}

?>