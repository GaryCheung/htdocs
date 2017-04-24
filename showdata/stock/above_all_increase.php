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
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">股票涨幅高于加权涨幅(运行时间：)</h1>

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

$date = '2017-04-21';

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

function all_stock_yesterday($date){
	$i = 0;
	$sql = "select distinct(date) from `stock_data` where date < '$date' and source = 'xueqiu' order by date desc limit 2";
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		$day[$i++] = $row[0];
		#echo $date;
	}
	print_r($day);
	$len = sizeof($day);
	$len--;
	$sql = "select * from `stock_data` where date = '$day[$len]' and source = 'xueqiu'";
	echo $sql;
	echo "<br>";
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
				#echo "yes";
				#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name = $stock_code[0];
		$list[$name] = $row[7];
		#echo $date;
	}
	return $list;
}

function all_stock_today($date){
	
	$sql = "select date from `stock_data` where date <= '$date' and source = 'xueqiu' order by date desc limit 1";
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		$date = $row[0];
		#echo $date;
	}

	$sql = "select * from `stock_data` where date = '$date' and source = 'xueqiu'";
	echo $sql;
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
				#echo "yes";
				#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name = $stock_code[0];
		$list[$name] = $row[7];
		#echo $date;
	}
	return $list;
}

function increase($list_yesterday,$list_today){
	foreach ($list_today as $key => $value) {
		$rate[$key] = $value / $list_yesterday[$key] - 1;
	}
	return $rate;
}

function distance($rate,$date){
	$sql = "select date from `stock_data` where date <= '$date' and source = 'xueqiu' order by date desc limit 1";
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		$date = $row[0];
		#echo $date;
	}
	$sql = "select * from `analysis` where date = '$date' and reason like '%total%'";
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		$performance = $row[1];
		#echo $date;
	}	
	foreach ($rate as $key => $value) {
		$dis[$key] = $value*100 - $performance; 
	}
	return $dis;
}

function insert_data($dis,$date){
	$sql = "delete from analysis where date = '$date' and reason = 'distance_positive'";
	#echo "<br>";
	#echo $sql;
	$res = Run_sql($sql);
	$i = 0;
	foreach ($dis as $key => $value) {
		if ($value > 0){
			$i++;
			$sql = "insert into analysis (name, date, reason) values ('$key', '$date', 'distance_positive')";
			#echo $sql;
			$res = Run_sql($sql);
			$string = substr($key, 1, 2).substr($key, 4, -1);
			$url = 'xueqiu.com/S/'.$string;
			echo "<li class='show'><a href='http://$url' style='color:#ddd'>$key</a></li>";	
		}	
	}
	echo "<br>";
	echo "<h1 style='font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px'>涨幅高于加权涨幅的股票共 $i 只</h1>";
}


$list_yesterday = all_stock_yesterday($today);

$list_today = all_stock_today($today);

$rate = increase($list_yesterday,$list_today);
#print_r($rate);

$result = distance($rate,$today);
#print_r($result);

insert_data($result,$date);



















?>
</body>
</html>