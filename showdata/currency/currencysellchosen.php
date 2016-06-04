<?php

$currency = $_COOKIE["currency"];
# echo $currency;

$conn=mysql_connect("localhost","root","19860112");
if(!$conn){
	echo "连接失败";
}

mysql_select_db("currency",$conn);
mysql_query("set names utf8");


$sql="select * from `currency` where name like '{$currency}%' ";
#echo $sql;
$res=mysql_query($sql,$conn);
#print_r(mysql_fetch_array($res));

$j = 0;
while($row=mysql_fetch_row($res)){
	$price[$j] = $row[4];
	$date[$j++] = $row[2]; 
}

$finall = array_merge($price,$date);

$json_string = json_encode($finall);
print_r($json_string);

?>