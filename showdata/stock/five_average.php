<html>
<head>
	<style type='text/css'>
	.wrapper {
		text-align: center;
	}

	li{
		width: 20%;
		margin: 3px;
	}

	.search{
		text-align: center;
		margin: 10px;
	}

	.input{
		color:#ddd;
	}

	.show{
		list-style: none;
		text-align: center;
		margin-left: 40%;
		margin-top: 10px;
		color:#ddd;
	}
	</style>
</head>

<body bgcolor="#32425c">
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">5日均线</h1>

	<div class="wrapper">
		<a href="/showdata/showall.php" style="text-align:center;color:#ddd">首页</a>
	</div>

<?php

#echo date("l");


$day = 12;
$begin = 0;
echo $begin;
for ($i=$begin;$i<$day;$i++){
	$date_array[$i] = date("Y-m-d",strtotime("-$i day"));
}
#print_r($date_array);

###
$conn=mysql_connect("localhost","root","root");
if(!$conn){
	echo "连接失败";
}

mysql_select_db("stock",$conn);
mysql_query("set names utf8");

$sql = "select * from `stock_data` where date = '$date_array[$begin]' and source = 'xueqiu' ";
#print($sql);

$res = mysql_query($sql,$conn);
#print($res);
while($row = mysql_fetch_row($res)){
	#print($row);
	$name = $row[1];
	$price[$name] = 0;
	#echo $price[$name];
}

$ma5 = 0;
$flag = 0;
$period = 5;
while ($ma5 < $period){
	$sql="select * from `stock_data` where date = '$date_array[$begin]' and source = 'xueqiu' ";
	$flag = 0;
	echo $sql;
	$res = mysql_query($sql,$conn);
	while($row = mysql_fetch_row($res)){
		if ($row[10] != 'Sunday' && $row[10] != 'Saturday'){
		$name = $row[1];
		$temp = (float)$row[7]; 
		$price[$name] += $temp;
		echo $price[$name];
		echo "\n";
		echo "########";
		$flag = 1;
		}
	}
	if ($flag == 1){
		$ma5++;
		#echo $ma5;
	}
	$begin++;
	#echo 'begin--------------------------------';
	#echo $begin;
}



?>