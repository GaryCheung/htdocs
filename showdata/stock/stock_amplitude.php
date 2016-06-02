<?php

$stock = $_COOKIE["stock"];
# echo $stock;

$conn=mysql_connect("localhost","root","root");
if(!$conn){
	echo "连接失败";
}

mysql_select_db("stock",$conn);
mysql_query("set names utf8");


$sql="select * from `stock_data` where stock_name like '{$stock}%' and source = 'xueqiu' ";
#echo $sql;
$res=mysql_query($sql,$conn);
#print_r(mysql_fetch_array($res));

$j = 0;
while($row=mysql_fetch_row($res)){
	$quantity[$j] = $row[2];
	$date[$j++] = $row[4]; 
}

$finall = array_merge($quantity,$date);

$json_string = json_encode($finall);
print_r($json_string);

?>