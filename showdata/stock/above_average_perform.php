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
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">价格高于5日均线 & 高于当日加权涨幅(运行时间：)</h1>

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

function select_data($today){
	$sql = "select distinct(date) from `analysis` where date <= '$today' and reason like '%distance_positive' order by date desc limit 1";
	#echo $sql;
	$res = Run_sql($sql);
	$i = 0;
	while($row = mysql_fetch_row($res)){
		$date = $row[0];
		#print_r($date);
		echo $date;
	}

	$sql = "select * from analysis where date = '$date' and reason like '%distance_positive%'";
	echo $sql;
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		$name = $row[1];
		#echo $name;
		#echo "<br>";
		$result[$name] = 1;
		#echo $result[$name];
	}
	#print_r($result);

	$sql = "select * from `analysis` where date = '$date' and reason like '%above_5average%'";
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		$name = $row[1];
		$result[$name]++;
		#echo $date;
	}
	return $result;
}

function insert_data($result,$today){
	$sql = "delete from analysis where date = '$today' and reason = 'above_average_perform'";
	echo $sql;
	Run_sql($sql);
	$i = 0;
	foreach ($result as $key => $value) {
		if ($value == 2){
			$i++;
			$sql = "insert into analysis (name, date, reason) values ('$key', '$today', 'above_average_perform')";
			#echo $sql;
			$res = Run_sql($sql);
			$string = substr($result[$len], 1, 2).substr($result[$len], 4, -1);
			$url = 'xueqiu.com/S/'.$string;
			echo "<li class='show'><a href='http://$url' style='color:#ddd'>$key</a></li>";	
		}
	}
	echo "<br>";
	echo "<h1 style='font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px'>涨幅高于加权涨幅的股票共 $i 只</h1>";
}

$result = select_data($today);
#print_r($result);

insert_data($result,$date);



























?>
</body>
</html>