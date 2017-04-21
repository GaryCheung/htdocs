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
$before_yesterday = date("Y-m-d",strtotime("-2 day"));

$date = '2017-04-18';

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

function stock_price_min($stock_name){
	$len = sizeof($stock_name);
	echo $len;
	while($len-- > 0){
		$name = $stock_name[$len];
		$sql = "select * from `stock_data` where stock_name like '%$name%'";
		#echo $sql;
		$res = Run_sql($sql);
		$price_min = 1000000;
		while($row = mysql_fetch_row($res)){
			$price = $row[7];
			$date = $row[4];
			#echo $price;
			#echo "<br>";
			if ($price <= $price_min && $price != 0){
				$price_min = $price;
				#echo $date;
				#echo "<br>";
			}
		}
		$list[$name] = $price_min;
		#echo $list[$name];
		#echo "<br>";
		#echo $price_min;
		#echo "<br>";
	}
	return $list;
}

function stock_price_min_yesterday($stock_name,$yesterday,$today){
	$len = sizeof($stock_name);
	echo $len;
	while($len-- > 0){
		$name = $stock_name[$len];
		$sql = "select * from `stock_data` where stock_name like '%$name%' and date = '$yesterday'";
		#echo $sql;
		$res = Run_sql($sql);
		while($row = mysql_fetch_row($res)){
			$price_min = $row[11];
			#echo $price_min;
		}
		$list[$name] = $price_min;
		#echo $list[$name];
		#echo "<br>";
		#echo $price_min;
		#echo "<br>";
	}
	$len = sizeof($stock_name);
	echo $len;
	while($len-- > 0){
		$name = $stock_name[$len];
		$sql = "select * from `stock_data` where stock_name like '%$name%' and date = '$today'";
		#echo $sql;
		$res = Run_sql($sql);
		while($row = mysql_fetch_row($res)){
			if ($list[$name] >= $row[7]){
				$list[$name] = $row[7];
			}
		}
	}
	return $list;	
}

function update_lowest_price($list,$today){
	echo $today;
	foreach ($list as $key => $value) {
		$name = $key;
		$sql = "update stock_data set history_lowest_price = '$value' where stock_name like '%$name%' and date = '$today'";
		#echo $sql;
		$res = Run_sql($sql);
	}
}

function position($list,$stock_name,$today){
	$len = sizeof($stock_name);
	echo $len;
	while($len-- > 0){
		$name = $stock_name[$len];
		$sql = "select * from `stock_data` where stock_name like '%$name%' order by date desc limit 1";
		#echo $sql;
		$res = Run_sql($sql);
		while($row = mysql_fetch_row($res)){
			$stock_level = $list[$name] / $row[7] * 100;
			#echo "<br>";
			#echo $stock_level;
			#echo "<br>";
			$sql = "update stock_data set price_level = '$stock_level' where stock_name like '%$name%' and date = '$today'";
			$res = Run_sql($sql);
			if ($stock_level < 30){
				$string = substr($stock_name[$len], 1, 2).substr($stock_name[$len], 4, -1);
				$url = 'xueqiu.com/S/'.$string;
				echo "<li class='show'><a href='http://$url' style='color:#ddd'>$stock_name[$len]</a></li>";
			}
		}	
	}
}

$stock_name = all_stock($today);
#print_r($stock_name);
echo 'ALL_STOCK DONE!!!!';

#$list = stock_price_min($stock_name);
#print_r($list);

$list = stock_price_min_yesterday($stock_name,$yesterday,$today);
#print_r($list);
echo 'LIST DONE!!!';

update_lowest_price($list,$today);
echo 'UPDATE DATA DONE';

position($list,$stock_name,$today);
echo 'POSITION DONE!!!!';







































?>
</body>
</html>