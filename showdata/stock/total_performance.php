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
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">今日所有股票加权涨幅</h1>

	<div class="wrapper">
		<a href="/showdata/showall.php" style="text-align:left;color:#ddd">首页</a>
	</div>

<?php

############ 参数区  ###############
$day = 20;
$begin = 0;
echo $begin;
$today = date("Y-m-d");
#$today = '2017-01-26';
$days = 2;      ####### 最近days天 ########


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

function Last_deal_date($today,$days){
	$sql = "select distinct(date) from stock_data where date <= '$today' order by date DESC limit $days";
	$res = Run_sql($sql);
	$i = 0;
	while($row = mysql_fetch_row($res)){
		$cmp_date[$i++] = $row[0]; 
	}
	return $cmp_date;
}

function Get_date($day, $today){
	$date_array = Last_deal_date($today, $day);
		return $date_array;
}

function Stock_list($date_array){
	$sql="select * from `stock_data` where date = '$date_array[0]' and source = 'xueqiu' ";
	#echo $sql;
	echo "<br>";
	$res = Run_sql($sql);
	$i = 0;
	while($row = mysql_fetch_row($res)){
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
				#echo "yes";
				#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$list[$i++] = $stock_code[0];
	}
	return $list;
}

function Get_price($date){
	$sql="select * from `stock_data` where date = '$date' and source = 'xueqiu' ";
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		#print_r($row);
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name_code = $stock_code[0];
		$name_open = (string)$stock_code[0].'name_open';
		$name_close = (string)$stock_code[0].'name_close';
		$name_date = (string)$stock_code[0].'name_date';
		$name_low = (string)$stock_code[0].'name_low';
		$name_high = (string)$stock_code[0].'name_high';
		$price[$name_open] = $row[6];
		$price[$name_close] = $row[7];
		$price[$name_date] = $row[4];
		$price[$name_low] = $row[9];
		$price[$name_high] = $row[8];
	}
	return $price;		
}

function Today_performance($price,$date){
	$sql="select * from `stock_data` where date = '$date' and source = 'xueqiu' ";
	$res = Run_sql($sql);
	$flag = 0;
	$sum = 0;
	while($row = mysql_fetch_row($res)){
		#print_r($row);
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name_code = $stock_code[0];
		$name_open = (string)$stock_code[0].'name_open';
		$name_close = (string)$stock_code[0].'name_close';
		$sum = $sum + ($price[$name_close] / $price[$name_open] -1)*100;
		$flag++;
	}
	return $sum/$flag;	
}

function Insert_data($result,$date_array,$begin){
	#echo "BEGIN";
	#echo $date_array[$begin];
	$sql = "delete from analysis where date = '$date_array[$begin]' and reason = 'total'";
	echo $sql;
	Run_sql($sql);
	echo "<p style='text-align:center;color:#ddd;font-size:20px'>今日股票加权涨幅 $result</p>";
	#echo $len;
	#echo "<br>";
	$sql = "insert into analysis (name, date, reason) values ('$result', '$date_array[$begin]', 'total')";
	$res = Run_sql($sql);
}

$date_array = Get_date($day,$today);

$price = Get_price($date_array[0]);

$performance = Today_performance($price,$date_array[0]);

Insert_data($performance,$date_array,$begin);

?>
</body>
</html>











