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
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">成交量 > 0 & 涨幅 > 0 </h1>

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
	$sql = "select distinct(date) from stock_data where date <= '$today' order by date desc limit 5";
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

function get_increase_positive($date){
	$sql = "select * from stock_data where date = '$date[0]'";
	echo "<br>";
	echo "GET_INCREASE_TODAY_DATE--------";
	echo $sql;
	echo "<br>";
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		if (preg_match("/\(+\w*\W+\w*\)+/", $row[1], $stock_code)){
				#echo "yes";
				#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
		};
		$name = $stock_code[0];
		$price_close_today[$name] = $row[7];
	}

	$sql = "select * from stock_data where date = '$date[1]'";
	echo "<br>";
	echo "GET_INCREASE_YESTERDAY_DATE--------";
	echo $sql;
	echo "<br>";
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		if (preg_match("/\(+\w*\W+\w*\)+/", $row[1], $stock_code)){
				#echo "yes";
				#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
		};
		$name = $stock_code[0];
		$price_close_yesterday[$name] = $row[7];
	}

	foreach ($price_close_today as $key => $value) {
		if ($price_close_yesterday[$key] > 0){
			/*
			echo "<br>";
			echo "NAME-----------";
			echo $key;
			echo "<br>";
			echo "TODAY PRICE!!!!!";
			echo $price_close_today[$key];
			echo "<br>";
			echo "YESTERDAY PRICE!!!!!!";
			echo $price_close_yesterday[$key];
			echo "<br>";
			*/
			$increase_today[$key] = ($price_close_today[$key] - $price_close_yesterday[$key]) / $price_close_yesterday[$key] * 100;
			#echo $increase_today[$key];
			#echo "<br>";
		}
	}
	return $increase_today;
}

function get_quantity_bigger($date){
	$sql = "select * from stock_data where date = '$date[0]'";
	echo "<br>";
	echo "GET_QUANTITY_TODAY_DATE--------";
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

	$sql = "select * from stock_data where date = '$date[1]'";
	echo "<br>";
	echo "GET_QUANTITY_YESTERDAY_DATE--------";
	echo $sql;
	echo "<br>";
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		if (preg_match("/\(+\w*\W+\w*\)+/", $row[1], $stock_code)){
				#echo "yes";
				#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
		};
		$name = $stock_code[0];
		$quantity_yesterday[$name] = $row[3];
	}

	foreach ($quantity_today as $key => $value) {
		if ($quantity_yesterday[$key] < $value ){
			/*
			echo "<br>";
			echo "NAME-----------";
			echo $key;
			echo "<br>";
			echo "TODAY PRICE!!!!!";
			echo $price_close_today[$key];
			echo "<br>";
			echo "YESTERDAY PRICE!!!!!!";
			echo $price_close_yesterday[$key];
			echo "<br>";
			*/
			$quantity_bigger[$key] = $key;
 			#echo $increase_today[$key];
			#echo "<br>";
		}
	}
	return $quantity_bigger;
}

function quantity_increase($increase_today,$quantity_bigger){
	foreach ($quantity_bigger as $key => $value) {
		if ($increase_today[$key] > 0){
			$result[$key] = $key;
		}
	}
	return $result;
}

function Insert_data($result,$date){
	#echo "BEGIN";
	#echo $date_array[$begin];
	$sql = "delete from analysis where date = '$date[0]' and reason = 'quantity_increase'";
	echo $sql;
	Run_sql($sql);
	$total = sizeof($result);
	echo "<p style='text-align:center;color:#ddd;font-size:20px'>共 $total 支股票</p>";
	#echo $total;
	#echo "<br>";
	foreach ($result as $key => $value) {
		$sql = "insert into analysis (name, date, reason) values ('$key', '$date[0]', 'quantity_increase')";
		#echo $sql;
		$res = Run_sql($sql);
		$string = substr($value, 1, 2).substr($value, 4, -1);
		$url = 'xueqiu.com/S/'.$string;
		echo "<li class='show'><a href='http://$url' style='color:#ddd'>$key</a></li>";	
	}
}


$date = get_date($today);
#print_r($date);

$increase_today = get_increase_positive($date);
#print_r($result);

$quantity_bigger = get_quantity_bigger($date);

$result = quantity_increase($increase_today,$quantity_bigger);

Insert_data($result,$date);
















?>
</body>
</html>