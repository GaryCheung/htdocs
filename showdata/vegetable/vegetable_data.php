<?php

$vege = $_COOKIE["vegetable"];
# echo $vege;

$conn=mysql_connect("localhost","root","19860112");
if(!$conn){
	echo "连接失败";
}

mysql_select_db("vegetable",$conn);
mysql_query("set names utf8");


$sql="select * from `vegetable` where vegetable like '{$vege}%'";
#echo $sql;
$res=mysql_query($sql,$conn);
#print_r(mysql_fetch_array($res));

$j = 0;
while($row=mysql_fetch_row($res)){
	$price[$j] = $row[5];
	$date[$j++] = $row[6]; 
}

$finall = array_merge($price,$date);

$json_string = json_encode($finall);
print_r($json_string);

?>