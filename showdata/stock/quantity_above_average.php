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

	.result{
		float: left;
		color: #ddd;
	}
	</style>
</head>

<body bgcolor="#32425c">
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">成交量高于前10日平均成交量</h1>

	<div class="wrapper">
		<a href="/showdata/showall.php" style="text-align:left;color:#ddd">首页</a>
	</div>

<?php

############ 参数区  ###############
$today = date("Y-m-d");
echo $today;
echo "<br>";

$yesterday = date("Y-m-d",strtotime("-1 day"));
echo $yesterday;
echo "<br>";

$before_yesterday = date("Y-m-d",strtotime("-2 day"));
echo $yesterday;
echo "<br>";

function Run_sql($sql){
	$conn=mysql_connect("localhost","root","root");
	if(!$conn){
		echo "连接失败";
	}
	mysql_select_db("stock",$conn);
	mysql_query("set names utf8");
	$res = mysql_query($sql,$conn);
	return $res;
}

function get_date($today){
	$sql = "select distinct(date) from stock_data where date <= '$today' order by date desc limit 10";
	#echo $sql;
	$res = Run_sql($sql);
	$i = 0;
	while($row = mysql_fetch_row($res)){
		$date[$i++] = $row[0];
		#print_r($date);
		#echo $date;
	}
	return $date;
}

function get_stockname($date){
	$sql = "select * from stock_data where date = '$date[0]'";
	echo "<br>";
	echo $sql;
	echo "<br>";
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		if (preg_match("/\(+\w*\W+\w*\)+/", $row[1], $stock_code)){
				#echo "yes";
				#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
		};
		$name = $stock_code[0];
		$quantity[$name] = 0;
	}
}

function default_count($date){
	$sql = "select * from stock_data where date = '$date[0]'";
	echo "<br>";
	echo $sql;
	echo "<br>";
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		if (preg_match("/\(+\w*\W+\w*\)+/", $row[1], $stock_code)){
				#echo "yes";
				#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
		};
		$name = $stock_code[0];
		$count[$name] = 0;
	}
}

function get_quantity_average($date,$quantity,$count){
	$len = sizeof($date);
	while ($len-- > 0){
		$sql = "select * from stock_data where date = '$date[$len]'";
		echo "<br>";
		echo $sql;
		echo "<br>";
		$res = Run_sql($sql);
		while($row = mysql_fetch_row($res)){
			if (preg_match("/\(+\w*\W+\w*\)+/", $row[1], $stock_code)){
				#echo "yes";
				#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
		};
			$name = $stock_code[0];
			$quantity[$name] += $row[3];
			$count[$name]++;
		}
	}
	foreach ($quantity as $key => $value) {
		$quantity_average[$key] = $value / $count[$key];
	}
	return $quantity_average;
}

function get_quantity_today($date){
	$sql = "select * from stock_data where date = '$date[0]'";
	echo "<br>";
	echo $sql;
	echo "<br>";
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		if (preg_match("/\(+\w*\W+\w*\)+/", $row[1], $stock_code)){
				#echo "yes";
				#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
		};
		$name = $stock_code[0];
		$quantity_today[$name] = $row[3];
	}
	return $quantity_today;
}

function quantity_above_10average($quantity_average,$quantity_today){
	foreach ($quantity_today as $key => $value) {
		if ($value > $quantity_average[$key] * 1.5){
			$result[$key] = $key;
		}
	}
	return $result;
}

function Insert_data($result,$date){
	#echo "BEGIN";
	#echo $date_array[$begin];
	$sql = "delete from analysis where date = '$date[0]' and reason = 'strategy_two'";
	echo $sql;
	Run_sql($sql);
	$total = sizeof($result);
	echo "<p style='text-align:center;color:#ddd;font-size:20px'>共 $total 支股票</p>";
	#echo $total;
	#echo "<br>";
	foreach ($result as $key => $value) {
		$sql = "insert into analysis (name, date, reason) values ('$key', '$date[0]', 'strategy_two')";
		#echo $sql;
		$res = Run_sql($sql);
		$string = substr($value, 1, 2).substr($value, 4, -1);
		$url = 'xueqiu.com/S/'.$string;
		echo "<li class='show'><a href='http://$url' style='color:#ddd'>$key</a></li>";	
	}
}

$date = get_date($today);

$quantity = get_stockname($date);

$count = default_count($date);

$quantity_average = get_quantity_average($date,$quantity,$count);

$quantity_today = get_quantity_today($date);

$result = quantity_above_10average($quantity_average,$quantity_today);

Insert_data($result,$date);















?>
</body>
</html>