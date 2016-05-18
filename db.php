<?php
//在表格中显示表的数据，常用方式
    function ShowTable($table_name){
        $conn=mysql_connect("localhost","root","root");
        if(!$conn){
            echo "连接失败";
        }
        mysql_select_db("gold_price",$conn);
        mysql_query("set names utf8");
        $sql="select * from $table_name";
        $res=mysql_query($sql,$conn);
        $rows=mysql_affected_rows($conn);//获取行数
        $colums=mysql_num_fields($res);//获取列数
        echo "gold_price数据库的"."gold_price表"."表的所有用户数据如下：<br/>";
        echo "共计".$rows."行 ".$colums."列<br/>";
         
        echo "<table style='border-color: #efefef;' border='1px' cellpadding='5px' cellspacing='0px'><tr>";
        for($i=0; $i < $colums; $i++){
            $field_name=mysql_field_name($res,$i);
            echo "<th>$field_name</th>";
        }
        echo "</tr>";
        while($row=mysql_fetch_row($res)){
            echo "<tr>";
            for($i=0; $i<$colums; $i++){
                echo "<td>$row[$i]</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    ShowTable("gold_price");

echo "Finally!!!!!"
?>