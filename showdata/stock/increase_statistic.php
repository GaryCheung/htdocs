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
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">股票涨幅分布图</h1>

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
	$sql = "select distinct(date) from stock_data where date <= '$today' order by date desc limit 2";
	echo $sql;
	$res = Run_sql($sql);
	$i = 0;
	while($row = mysql_fetch_row($res)){
		$date[$i++] = $row[0];
		#print_r($date);
		#echo $date;
	}
	return $date;
}

function get_yesterday_data($date){
	$sql = "select * from stock_data where date = '$date[1]'";
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
		$yesterday_close_price[$name] = $row[7];
	}
	return $yesterday_close_price;
}

function get_today_data($date){
	$sql = "select * from stock_data where date = '$date[0]'";
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
		$today_close_price[$name] = $row[7];
	}
	return $today_close_price;
}

function increase_rate($yesterday_close_price,$today_close_price){
	$len = 22;
	while ($len-- > 0){
		$list[$len] = 0;
	}
	foreach ($today_close_price as $key => $value) {
		$rate[$key] = ($value - $yesterday_close_price[$key]) / $yesterday_close_price[$key] * 100; 
		#echo $rate[$key];
		#echo "<br>";
		if ($rate[$key] >= 0){
			switch (floor($rate[$key])) {
				case 0:
					$list[0]++;
					$stock_name[$key] = $rate[$key];
					break;
				case 1:
					$list[1]++;
					$stock_name[$key] = $rate[$key];
					break;
				case 2:
					$list[2]++;
					$stock_name[$key] = $rate[$key];
					break;
				case 3:
					$list[3]++;
					$stock_name[$key] = $rate[$key];
					break;
				case 4:
					$list[4]++;
					$stock_name[$key] = $rate[$key];
					break;
				case 5:
					$list[5]++;
					$stock_name[$key] = $rate[$key];
					break;
				case 6:
					$list[6]++;
					$stock_name[$key] = $rate[$key];
					break;
				case 7:
					$list[7]++;		
					$stock_name[$key] = $rate[$key];
					break;
				case 8:
					$list[8]++;	
					$stock_name[$key] = $rate[$key];
					break;
				case 9:
					$list[9]++;	
					$stock_name[$key] = $rate[$key];
					break;
				case 10:
					$list[10]++;
					$stock_name[$key] = $rate[$key];
					break;
			}			
		}
		else {
			switch (floor($rate[$key])) {
				case 0:
					$list[11]++;
					break;
				case -1:
					$list[12]++;
					break;
				case -2:
					$list[13]++;
					break;
				case -3:
					$list[14]++;
					break;
				case -4:
					$list[15]++;
					break;
				case -5:
					$list[16]++;
					break;
				case -6:
					$list[17]++;
					break;
				case -7:
					$list[18]++;
					break;		
				case -8:
					$list[19]++;	
					break;
				case -9:
					$list[20]++;	
					break;
				case -10:
					$list[21]++;	
					break;	
			}			
		}
	}
	$back_data[0] = $list;
	$back_data[1] = $stock_name;
	return $back_data;
}

function draw_data($result){
	$list = $result[0];
	$i = 0;
	while($i < 22){
		if ($i <= 10){
			$upper = $i+1;
			$lower = $i;
			echo "<li class='show'><a style='color:#dd0'>涨幅$lower ~ $upper 股票数 $list[$i]</a></li>";	
			$i++;
		}
		else{
			$upper = 11 - $i;
			$lower = 10 - $i;
			echo "<li class='show'><a style='color:#dd0'>涨幅$lower ~ $upper 股票数 $list[$i]</a></li>";	
			$i++;
		}
	}
}

function Insert_data($result,$today){
	#echo "BEGIN";
	#echo $date_array[$begin];
	$sql = "delete from analysis where date = '$today' and reason = 'increase_rate_positive'";
	echo $sql;
	Run_sql($sql);
	$i = 0;
	$total = 0;
	$list = $result[0];
	while($i <= 10){
		$total = $total + $list[$i++];
	}
	echo "<p style='text-align:center;color:#ddd;font-size:20px'>涨幅>0 , 共 $total 支股票</p>";
	#echo $total;
	#echo "<br>";
	$out = $result[1];
	foreach ($out as $key => $value) {
		$sql = "insert into analysis (name, date, reason) values ('$key', '$today', 'increase_rate_positive')";
		#echo $sql;
		$res = Run_sql($sql);
		$string = substr($value, 1, 2).substr($value, 4, -1);
		$url = 'xueqiu.com/S/'.$string;
		echo "<li class='show'><a href='http://$url' style='color:#ddd'>$key 涨幅  $value</a></li>";	
	}
}

$date = get_date($today);
#print_r($date);

$yesterday_close_price = get_yesterday_data($date);
#print_r($yesterday_close_price);

$today_close_price = get_today_data($date);

$result = increase_rate($yesterday_close_price,$today_close_price);
#print_r($list);

draw_data($result);
echo "<br>";

Insert_data($result,$date[0]);























?>
</body>
</html>