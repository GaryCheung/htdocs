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
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">股票价格位置</h1>

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
	while($len-- > 0){
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

function above_5average($today,$stock_name,$list){
	$len = sizeof($stock_name);
	echo $len;
	echo "<br>";
	$i = 0;
	while($len-- > 0){
		$name = $stock_name[$len];
		#echo $name;
		$sql = "select * from `stock_data` where date = '$today' and source = 'xueqiu' and stock_name like '%$name%'";
		#echo $sql;
		$res = Run_sql($sql);
		while($row = mysql_fetch_row($res)){
			if ($list[$name] < $row[7]){
				$result[$i++] = $name;
			}
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

$stock_name = all_stock($yesterday);
#print_r($stock_name);
echo "<br>";
echo "ALL_STOCK DONE!!!!";
echo "<br>";

$list = get_5average($yesterday,$stock_name);
print_r($list);
echo "<br>";
echo "GET_5AVERAGE DONE!!!";
echo "<br>";

$result = above_5average($yesterday,$stock_name,$list);
print_r($result);
echo "<br>";
echo "ABOVE_5AVERAGE DONE!!!";
echo "<br>";

Insert_data($result,$yesterday);





































?>
</body>
</html>