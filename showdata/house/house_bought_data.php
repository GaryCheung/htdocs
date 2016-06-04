<?php

# print_r($_COOKIE);

$house = $_COOKIE[bought][house];
$layout = $_COOKIE[bought][layout];

# echo $house;

$conn=mysql_connect("localhost","root","19860112");
if(!$conn){
	echo "连接失败";
}

mysql_select_db("house_bought",$conn);
mysql_query("set names utf8");


$sql="select * from `house_bought` where name like '{$house}%' and layout like '%{$layout}%' order by bought_date asc";
# echo $sql;
$res=mysql_query($sql,$conn);
# print_r(mysql_fetch_array($res));

$j = 0;
while($row=mysql_fetch_row($res)){
	$price[$j] = $row[5];
	$date[$j++] = $row[7]; 
}

$finall = array_merge($price,$date);

$json_string = json_encode($finall);
print_r($json_string);

?>