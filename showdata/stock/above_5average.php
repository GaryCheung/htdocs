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
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">股价在5日均线之上(运行时间：1小时30分钟)</h1>

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

function all_stock($date){
	$i = 0;
	$sql = "select * from `stock_data` where date = '$date' and source = 'xueqiu' ";
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
				#echo "yes";
				#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$list[$i] = $stock_code[0];
		#echo $list[$i];
		#echo "<br>";
		$i++;
	}
	return $list;
}

function get_5average($today,$stock_name){
	$len = sizeof($stock_name);
	echo $len;
	echo "<br>";
	$flag = 0;
	while($len-- > 0){
		echo "<br>";
		echo $flag++;
		echo "<br>";
		$name = $stock_name[$len];
		$sql = "select * from `stock_data` where date <= '$today' and source = 'xueqiu' and stock_name like '%$name%' order by date desc limit 5";
		$res = Run_sql($sql);
		$average = 0;
		while($row = mysql_fetch_row($res)){
			$average = $average + $row[7];
		}
		$average = $average / 5;
		$list[$name] = $average;
	}
	return $list;
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

function get_5average_simple($date){
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

function above_5average($today,$list){
	$i = 0;
	$sql = "select * from `stock_data` where date = '$today' and source = 'xueqiu'";
	#echo $sql;
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name = $stock_code[0];
		#echo $name;
		#echo "<br>";
		if ($list[$name] < $row[7] && $list[$name] != 0){
				$result[$i++] = $name;
			}
	}
	return $result;
}

function Insert_data($result,$today){
	#echo "BEGIN";
	#echo $date_array[$begin];
	$sql = "delete from analysis where date = '$today' and reason = 'above_5average'";
	echo $sql;
	Run_sql($sql);
	$len = sizeof($result);
	echo "<p style='text-align:center;color:#ddd;font-size:20px'>共 $len 支股票</p>";
	echo $len;
	echo "<br>";
	while ($len-- > 0){
		#echo $len;
		#echo $result[$len];
		#echo "<br>";
		$sql = "insert into analysis (name, date, reason) values ('$result[$len]', '$today', 'above_5average')";
		#echo $sql;
		$res = Run_sql($sql);
		$string = substr($result[$len], 1, 2).substr($result[$len], 4, -1);
		$url = 'xueqiu.com/S/'.$string;
		echo "<li class='show'><a href='http://$url' style='color:#ddd'>$result[$len]</a></li>";	
	}
}

$stock_name = all_stock($today);
#print_r($stock_name);
echo "<br>";
echo "ALL_STOCK DONE!!!!";
echo "<br>";

#$list = get_5average($today,$stock_name);
$date = get_date($today);
$list = get_5average_simple($date);
#print_r($list);
echo "<br>";
echo "GET_5AVERAGE DONE!!!";
echo "<br>";

$result = above_5average($today,$list);
#print_r($result);
echo "<br>";
echo "ABOVE_5AVERAGE DONE!!!";
echo "<br>";

Insert_data($result,$today);





?>
</body>
</html>