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
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">收盘价>5日均线 & 涨幅大于全部股票加权涨幅 & 涨幅>0</h1>

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

function get_5average($date){
	$len = sizeof($date);
	while ($len-- > 0){
		$sql = "select * from stock_data where date = '$date[$len]'";
		echo "<br>";
		echo "5_AVERAGE_PERIOD------";
		echo $sql;
		echo "<br>";
		$res = Run_sql($sql);
		while($row = mysql_fetch_row($res)){
			if (preg_match("/\(+\w*\W+\w*\)+/", $row[1], $stock_code)){
				#echo "yes";
				#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
			$name = $stock_code[0];
			$data[$len][$name] = $row[7];
		}
	}
	foreach ($data[0] as $key => $value) {
		$average[$key] = ($value + $data[1][$key] + $data[2][$key] + $data[3][$key] + $data[4][$key]) / 5;
	}
	return $average;
}

function get_increase($date){
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

function strategy_one($date,$average,$increase_today){
	$sql = "select * from stock_data where date = '$date[0]'";
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

	$sql = "select * from analysis where date = '$date[0]' and reason like '%total%'";
	echo $sql;
	echo "<br>";
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		$increase_rate_total = $row[1];
	}
	#echo $increase_rate_total;	

	foreach ($price_close_today as $key => $value) {
		#echo "FIND ONE";
		#echo "<br>";
		/*
		echo "PRICE_CLOSE_TODAY -------";
		echo "<br>";
		echo $value;
		echo "<br>";
		echo "AVERAGE -------";
		echo "<br>";
		echo $average[$key];
		echo "<br>";
		echo "INCREASE_TODAY -------";
		echo "<br>";
		echo $increase_today[$key];
		echo "<br>";
		echo "INCREASE_RATE_TOTAL -------";
		echo "<br>";
		echo $increase_rate_total;
		echo "<br>";
		*/
		if ($value>$average[$key] && $increase_today[$key]>$increase_rate_total && $increase_today[$key]>0){
			#echo "<br>";
			#echo "FIND ONE!!!!!";
			$result[$key] = $key;
		}
	}

	return $result;
}



function Insert_data($result,$date){
	#echo "BEGIN";
	#echo $date_array[$begin];
	$sql = "delete from analysis where date = '$date[0]' and reason = 'strategy_one'";
	echo $sql;
	Run_sql($sql);
	$total = sizeof($result);
	echo "<p style='text-align:center;color:#ddd;font-size:20px'>共 $total 支股票</p>";
	#echo $total;
	#echo "<br>";
	foreach ($result as $key => $value) {
		$sql = "insert into analysis (name, date, reason) values ('$key', '$date[0]', 'strategy_one')";
		#echo $sql;
		$res = Run_sql($sql);
		$string = substr($value, 1, 2).substr($value, 4, -1);
		$url = 'xueqiu.com/S/'.$string;
		echo "<li class='show'><a href='http://$url' style='color:#ddd'>$key</a></li>";	
	}
}

$date = get_date($today);
#print_r($date);

$average = get_5average($date);
#print_r($average);

$increase_today = get_increase($date);
#print_r($increase_today);

$result = strategy_one($date,$average,$increase_today);
#print_r($result);

Insert_data($result,$date);

















?>
</body>
</html>