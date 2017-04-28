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
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">从最高位跌幅50%以上</h1>

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
	$sql = "select distinct(date) from stock_data where date <= '$today' order by date desc limit 1";
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

function get_period($date){
	$sql = "select distinct(date) from stock_data where date <= '$date[0]'";
	$res = Run_sql($sql);
	$i = 0;
	while($row = mysql_fetch_row($res)){
		$date[$i++] = $row[0];
		#print_r($date);
		#echo $date;
	}
	return $date;
}

function get_peak($period){
	$sql = "select * from stock_data where date = '$period[0]'";
	$res = Run_sql($sql);
	$i = 0;
	while($row = mysql_fetch_row($res)){
		if (preg_match("/\(+\w*\W+\w*\)+/", $row[1], $stock_code)){
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
		};
		$name = $stock_code[0];
		$peak[$name] = $row[7];
	}

	$len = sizeof($period);
	while ($len-- >0){
		$sql = "select * from stock_data where date = '$period[$len]'";
		$res = Run_sql($sql);
		$i = 0;
		while($row = mysql_fetch_row($res)){
			if (preg_match("/\(+\w*\W+\w*\)+/", $row[1], $stock_code)){
				#echo "yes";
				#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
			$name = $stock_code[0];
			if ($peak[$name] < $row[7]){
				$peak[$name] = $row[7];
			}
			#print_r($date);
			#echo $date;
		}
	}
	return $peak;
}

function drop_50($peak,$date){
	echo $date[0];
	echo "<br>";
	$sql = "select * from stock_data where date = '$date[0]'";
	echo $sql;
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		if (preg_match("/\(+\w*\W+\w*\)+/", $row[1], $stock_code)){
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
		};
		$name = $stock_code[0];
		/*
		echo "<br>";
		echo "PEAK PRICE----------";
		echo $peak[$name];
		echo "<br>";
		echo "TODAY PRICE---------";
		echo $row[7];
		echo "<br>";
		echo "CALULATION----------";
		echo ($row[7]-$peak[$name]) / $peak[$name];
		echo "<br>";
		*/
		if (($row[7]-$peak[$name]) / $peak[$name] <= -0.5){
			$result[$name] = ($row[7]-$peak[$name]) / $peak[$name];
		}
	}
	return $result;
}

function Insert_data($result,$date){
	#echo "BEGIN";
	#echo $date_array[$begin];
	$sql = "delete from analysis where date = '$date[0]' and reason = 'drop_50'";
	echo $sql;
	Run_sql($sql);
	$total = sizeof($result);
	echo "<p style='text-align:center;color:#ddd;font-size:20px'>共 $total 支股票</p>";
	#echo $total;
	#echo "<br>";
	foreach ($result as $key => $value) {
		$sql = "insert into analysis (name, date, reason) values ('$key', '$date[0]', 'drop_50')";
		#echo $sql;
		$res = Run_sql($sql);
		$string = substr($value, 1, 2).substr($value, 4, -1);
		$url = 'xueqiu.com/S/'.$string;
		echo "<li class='show'><a href='http://$url' style='color:#ddd'>$key</a></li>";	
	}
}

$date = get_date($today);
#print_r($date);

$period = get_period($date);
#print_r($result);

$peak = get_peak($period);
#print_r($peak);

$result = drop_50($peak,$date);
#print_r($result);

Insert_data($result,$date);













?>
</body>
</html>