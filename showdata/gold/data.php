<?php

$conn=mysql_connect("localhost","root","19860112");
if(!$conn){
	echo "连接失败";
}

mysql_select_db("gold_price",$conn);
mysql_query("set names utf8");

$sql="select * from `gold_price`";
$res=mysql_query($sql,$conn);
//print_r(mysql_fetch_array($res));

$j = 0;
while($row=mysql_fetch_row($res)){
	$data[$j] = $row[2];
	$date[$j++] = $row[1]; 
}

$finall = array_merge($data,$date);

$json_string = json_encode($finall);
print_r($json_string);

?>