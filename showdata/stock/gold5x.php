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
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">5日均线上穿10日均线的股票</h1>

	<div class="wrapper">
		<a href="/showdata/showall.php" style="text-align:center;color:#ddd">首页</a>
	</div>

<?php

#echo date("l");

$day = 30;
$begin = 0;
echo $begin;
########### 取最近30天的雪球数据  ###########

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
echo $sql;
#print($sql);

$res = mysql_query($sql,$conn);
#print($res);

############ 初始化5日均线和10日均线数组 ############
while($row = mysql_fetch_row($res)){
	#print($row);
	$name = $row[1];
	$price_5day[$name] = 0;
	$price_5day_yesterday[$name] = 0;
	$price_10day[$name] = 0;
	$price_10day_yesterday[$name] = 0;
	#echo $price_5day[$name];
	#echo "<br>";
	#echo $price_10day[$name];
}

$ma5 = 0;
$ma10 = 0;
$flag = 0;
$period_5 = 5;
$start = $begin;
############# 计算当日5日均线  ###################
while ($ma5 < $period_5){
	$sql="select * from `stock_data` where date = '$date_array[$start]' and source = 'xueqiu' ";
	echo "<br>";
	echo $sql;
	echo "<br>";
	$flag = 0;
	#echo $sql;
	$res = mysql_query($sql,$conn);
	while($row = mysql_fetch_row($res)){
		if ($row[10] != 'Sunday' && $row[10] != 'Saturday'){
		$name = $row[1];
		$temp = (float)$row[7]; 
		$price_5day[$name] += $temp;
		echo $price_5day[$name];
		echo "##########";
		$flag = 1;
		}
	}
	if ($flag == 1){
		$ma5++;
		#echo $ma5;
	}
	$start++;
	/*
	echo 'start--------------------------------';
	echo "start=";
	echo $start;
	echo "#######################   END BEGIN";
	echo "ma5=================";
	echo $ma5;
	echo "#######################    END MA5";
	echo "<br>";
	*/
}

############ 计算前日5日均线  ############
$ma5 = 0;
$flag = 0;
$period_5 = 5;
$start = $begin+1;
while ($ma5 < $period_5){
	$sql="select * from `stock_data` where date = '$date_array[$start]' and source = 'xueqiu' ";
	echo "<br>";
	echo $sql;
	echo "<br>";
	$flag = 0;
	#echo $sql;
	$res = mysql_query($sql,$conn);
	while($row = mysql_fetch_row($res)){
		if ($row[10] != 'Sunday' && $row[10] != 'Saturday'){
		$name = $row[1];
		$temp = (float)$row[7]; 
		$price_5day_yesterday[$name] += $temp;
		echo $price_5day_yesterday[$name];
		echo "##########";
		$flag = 1;
		}
	}
	if ($flag == 1){
		$ma5++;
		#echo $ma5;
	}
	$start++;
	/*
	echo 'start--------------------------------';
	echo "start=";
	echo $start;
	echo "#######################   END BEGIN";
	echo "ma5=================";
	echo $ma5;
	echo "#######################    END MA5";
	echo "<br>";
	*/
}

$ma10 = 0;
$flag = 0;
$period_10 = 10;
$start = $begin;
############# 计算10日均线  ###################
while ($ma10 < $period_10){
	$sql="select * from `stock_data` where date = '$date_array[$start]' and source = 'xueqiu' ";
	$flag = 0;
	#echo $sql;
	$res = mysql_query($sql,$conn);
	while($row = mysql_fetch_row($res)){
		if ($row[10] != 'Sunday' && $row[10] != 'Saturday'){
		$name = $row[1];
		$temp = (float)$row[7]; 
		$price_10day[$name] += $temp;
		echo $price_10day[$name];
		echo "##########";
		$flag = 1;
		}
	}
	if ($flag == 1){
		$ma10++;
		#echo $ma5;
	}
	$start++;
	/*
	echo 'start--------------------------------';
	echo "start=";
	echo $start;
	echo "#######################   END BEGIN";
	echo "ma10=================";
	echo $ma10;
	echo "#######################    END MA10";
	echo "<br>";
	*/
}

$ma10 = 0;
$flag = 0;
$period_10 = 10;
$start = $begin+1;
############# 计算前一交易日10日均线  ###################
while ($ma10 < $period_10){
	$sql="select * from `stock_data` where date = '$date_array[$start]' and source = 'xueqiu' ";
	$flag = 0;
	#echo $sql;
	$res = mysql_query($sql,$conn);
	while($row = mysql_fetch_row($res)){
		if ($row[10] != 'Sunday' && $row[10] != 'Saturday'){
		$name = $row[1];
		$temp = (float)$row[7]; 
		$price_10day_yesterday[$name] += $temp;
		echo $price_10day_yesterday[$name];
		echo "##########";
		$flag = 1;
		}
	}
	if ($flag == 1){
		$ma10++;
		#echo $ma5;
	}
	$start++;
	/*
	echo 'start--------------------------------';
	echo "start=";
	echo $start;
	echo "#######################   END BEGIN";
	echo "ma10=================";
	echo $ma10;
	echo "#######################    END MA10";
	echo "<br>";
	*/
}

########### 寻找5日均线上穿10日均线的股票 ############
$total = 0;
$today = date("Y-m-d");
$sql = "delete from analysis where date >= '$date_array[$begin]' and reason = 'gold5_10x'";
mysql_query($sql,$conn);
$sql="select * from `stock_data` where date = '$date_array[$begin]' and source = 'xueqiu' ";
#echo $sql;

$res = mysql_query($sql,$conn);
while($row = mysql_fetch_row($res)){
	$name = $row[1]; 
	#echo 'price[name]	',$price[$name];
	#echo 'row[7]	',$row[7];
	#echo 'row[6]	',$row[6];
	#echo '----------------------';
	if ($price_10day[$name]/$period_10 < $price_5day[$name]/$period_5 && $price_10day_yesterday[$name]/$period_10 > $price_5day_yesterday[$name]/$period_5){
		$five_ave = $price[$name]/$period;
		$total++;
		$sql = "insert into analysis (name, date, reason) values ('$name', '$date_array[$begin]', 'gold5_10x')";
		#echo $sql;
		mysql_query($sql,$conn);
		#echo $total;
	}
}

echo "<p style='text-align:center;color:#ddd;font-size:20px'>共 $total 支股票</p>";

$sql = "select * from `stock_data` where date = '$date_array[$begin]' and source = 'xueqiu' ";
$res = mysql_query($sql,$conn);
while($row=mysql_fetch_row($res)){
	$name = $row[1];
	if ($price_10day[$name]/$period_10 < $price_5day[$name]/$period_5 && $price_10day_yesterday[$name]/$period_10 > $price_5day_yesterday[$name]/$period_5){
		echo "<li class='show'><a href='/showdata/showall.php' style='color:#ddd'>$name</a></li>";
	}
}

?>
</body>
</html>

