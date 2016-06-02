<?php

# print_r($_COOKIE);

$house = $_COOKIE[cookie][house];
$layout = $_COOKIE[cookie][layout];

#echo $house,$layout;

$conn=mysql_connect("localhost","root","root");
if(!$conn){
	echo "连接失败";
}

mysql_select_db("house_rent",$conn);
mysql_query("set names utf8");


$sql="select * from `house_rent` where name like '{$house}%' and layout like '%{$layout}%' order by date asc";
#echo $sql;
$res=mysql_query($sql,$conn);
# print_r(mysql_fetch_array($res));

$j = 0;
while($row=mysql_fetch_row($res)){
	$price[$j] = $row[4];
	$date[$j++] = $row[6]; 
}

$finall = array_merge($price,$date);

$json_string = json_encode($finall);
print_r($json_string);

?>