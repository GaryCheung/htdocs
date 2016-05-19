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

        print_r(mysql_fetch_array($res));

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

        $j = 0;
        while($row=mysql_fetch_row($res)){
            $data[$j] = $row[2];
            $date[$j++] = $row[1]; 
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

<canvas id="canvas1" width="300" height="300">
        Your web-browser does not support the HTML 5 canvas element.
</canvas>

<script type="application/javascript" src="awesomechart.js"></script>
                    
<script type="application/javascript">
  function drawMyChart(){
    if(!!document.createElement('canvas').getContext){ //check that the canvas
                                                       // element is supported
        var mychart = new AwesomeChart('canvas1');
        mychart.title = "Product Sales - 2010";
        mychart.data = [1532, 3251, 3460, 1180, 6543];
        mychart.labels = ["Desktops", "Laptops", "Netbooks", "Tablets", "Smartphones"];
        mychart.draw();
    }
  }
  
  window.onload = drawMyChart;
</script>



