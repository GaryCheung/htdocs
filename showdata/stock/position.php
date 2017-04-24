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

function stock_price_min($stock_name,$today){
	$len = sizeof($stock_name);
	while ($len-- > 0){
		$name = $stock_name[$len];
		$price_min[$name] = 1000000;
	}
	#print_r($price_min);
	echo "INITIAL DONE!!!!";
	echo "<br>";
	$len = sizeof($stock_name);
	while ($len-- > 0){
		$name = $stock_name[$len];
		$sql = "select * from `stock_data` where stock_name like '%$name%' and date <= '$today'";
		#echo $sql;
		$res = Run_sql($sql);
		$flag = 0;
		while($row = mysql_fetch_row($res)){
			#echo "#################";
			#echo $flag++;
			#echo "#################";
			#echo "<br>";
			$price = $row[7];
			$date = $row[4];
			#echo $price;
			#echo "<br>";
			#echo $price_min[$stock_code[0]];
			#echo "<br>";
			if ($price <= $price_min[$name] && $price != 0){
				$price_min[$name] = $price;
				#echo $date;
				#echo "<br>";
				}
		}
		print_r($price_min);
		echo "##################";
		echo "<br>";
	}
	echo "PRICE_MIN DONE!!!!";
	echo "<br>";
	#print_r($price_min);
	return $price_min;
}

function stock_price_min_yesterday($today){
	$sql = "select * from `stock_extend` where date < '$today' order by date desc limit 1";
	echo $sql;
	echo "<br>";
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		$yesterday = $row[2];
	}
	$sql_stock_min = "select * from `stock_extend` where date = '$yesterday'";
	#echo $sql;
	$res = Run_sql($sql_stock_min);
	while($row = mysql_fetch_row($res)){
		$price_min = $row[11];
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
				#echo "yes";
				#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		#echo $price_min;
		$name = $stock_code[0];
		$list[$name] = $price_min;	
		#print_r($list);	
		#echo "<br>";
		#echo $name;
		#echo "###############";
		#echo "<br>";
	}
	$sql_stock_min = "select * from `stock_data` where date = '$today'";
	#echo $sql;
	$res = Run_sql($sql_stock_min);
	while($row = mysql_fetch_row($res)){
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name = $stock_code[0];
		if ($list[$name] >= $row[7] && $row[7] != 0){
				$list[$name] = $row[7];
			}
		#echo "<br>";
		#echo $name;
		#echo "###############";
		#echo "<br>";		
	}
	#print_r($list);
	#echo "<br>";
	#echo "DONE!!!!!!!!!!!!!";
	#echo "<br>";
	return $list;	
}

function update_lowest_price($list,$today){
	echo $today;
	echo "<br>";
	echo "###########";
	echo "<br>";
	#print_r($list);
	echo "<br>";
	$flag = 0;
	$sql_del = "delete from stock_extend where date = '$today'";
	echo $sql;
	$res = Run_sql($sql_del);
	foreach ($list as $key => $value) {
		#echo "<br>";
		#echo "@@@@@@@@@    FLAG     @@@@@@@@@@@@@@@";
		#echo $flag++;
		#echo "<br>";
		$name = $key;
		#echo "<br>";
		#echo $name;
		#echo "#############";
		#echo "<br>";
		$sql_update = "insert into stock_extend (stock_name, date, history_lowest_price) values ('$name', '$today', '$value')";
		#echo $sql;
		$res = Run_sql($sql_update);
	}
}

function position($list,$today){
	$sql_del = "delete from stock_extend where date = '$today'";
	echo $sql;
	$res = Run_sql($sql_del);
	$sql_position = "select * from `stock_data` where date = '$today'";
	echo $sql_position;
	$res = Run_sql($sql_position);
	#print_r($res);
	$flag = 0;
	while($row = mysql_fetch_row($res)){
		#print_r($row);
		echo "<br>";
		echo $flag++;
		echo "<br>";
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name = $stock_code[0];
		echo "<br>";
		echo $name;
		echo "<br>";
		$stock_level = $row[7] / $list[$name] * 100 - 100;
			#echo "<br>";
			#echo $stock_level;
			#echo "<br>";
		$sql_insert = "insert into stock_extend (stock_name, date, history_lowest_price, price_level) values ('$name', '$today', '$list[$name]', '$stock_level')";
		echo $sql_insert;
		echo "############";
		echo "<br>";
		$res_1 = Run_sql($sql_insert);
		if ($stock_level < 30){
			$string = substr($name, 1, 2).substr($name, 4, -1);
			$url = 'xueqiu.com/S/'.$string;
			echo "<li class='show'><a href='http://$url' style='color:#ddd'>$name</a></li>";
		}
	}	
}

#$stock_name = all_stock($yesterday);
#print_r($stock_name);
echo "<br>";
echo 'ALL_STOCK DONE!!!!';
echo "<br>";

#$list = stock_price_min($stock_name);
#print_r($list);


$list = stock_price_min_yesterday($today);
#print_r($list);
echo "<br>";
echo 'LIST DONE!!!';
echo "<br>";

/*
update_lowest_price($list,$yesterday);
echo "<br>";
echo 'UPDATE DATA DONE';
echo "<br>";
*/

position($list,$today);
echo "<br>";
echo 'POSITION DONE!!!!';
echo "<br>";




?>
</body>
</html>