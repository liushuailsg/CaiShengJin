<html>
    <head>
        <title>查询数据库表信息</title>
    </head>
    <body>
        <center>
            <form name="myForm" action="processShowData.php">
                <table  border="1" cellspacing="0" cellpadding="0">
                    <caption>学生一览表</caption>
                        <th>学号</th>
                        <th>姓名</th>
                        <th>年龄</th>
                        <th>性别</th>
                        <th>地址</th>
                        <?php
                            $link=mysql_connect("localhost","root","root");
                            mysql_select_db("rorely");
                            $exec="select * from test";
                            $result=mysql_query($exec);
                            while($rs=mysql_fetch_object($result)){
                                $id=$rs->id;
                                $name=$rs->name;
                                $age=$rs->age;
                                $sex=$rs->sex;
                                $address=$rs->address;
                        ?>
                        <tr align="center">
                        <td><?php echo $id ?></td>
                        <td><?php echo $name ?></td>
                        <td><?php echo $age ?></td>
                        <td><?php echo $sex ?></td>
                        <td><?php echo $address ?></td>
                        </tr>
                            <?php
                            }
                            mysql_close();
                            ?>
                </table>
            </form>
        </center>
    </body>
</html>