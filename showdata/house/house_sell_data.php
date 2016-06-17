<?php

# print_r($_COOKIE);

$house = $_COOKIE[sell][house];
$bottom = $_COOKIE[sell][areabottom];
$top = $_COOKIE[sell][areatop];

#echo $house,$bottom,$top;

$conn=mysql_connect("localhost","root","19860112");
if(!$conn){
	echo "连接失败";
}

mysql_select_db("house",$conn);
mysql_query("set names utf8");


if (!$bottom){
	$sql = "select * from `house` where house_name like '{$house}%' order by date asc";
}else{
	$sql = "select * from `house` where house_name like '{$house}%' and house_area >= '$bottom' and house_area <= '$top' order by date asc";
}
# echo $sql;
$res=mysql_query($sql,$conn);
# print_r(mysql_fetch_array($res));

$j = 0;
while($row=mysql_fetch_row($res)){
	$price[$j] = $row[6];
	$date[$j++] = $row[1]; 
}

$finall = array_merge($price,$date);

$json_string = json_encode($finall);
print_r($json_string);

?>