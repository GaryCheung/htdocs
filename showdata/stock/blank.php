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
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">吞没形态股票</h1>

	<div class="wrapper">
		<a href="/showdata/showall.php" style="text-align:left;color:#ddd">首页</a>
	</div>

<?php

############ 参数区  ###############
$day = 20;
$begin = 0;
echo $begin;
$today = date("Y-m-d");
$today = '2017-01-24';
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

function Is_blank($price_1, $price_2, $date){
	$i = 0;
	$sql="select * from `stock_data` where date = '$date' and source = 'xueqiu' ";
	#echo $sql;
	#echo "<br>";
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
				#echo "yes";
				#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name_date = (string)$stock_code[0].'name_date';
		$name_open = (string)$stock_code[0].'name_open';
		$name_close = (string)$stock_code[0].'name_close';	
		$name_low =	(string)$stock_code[0].'name_low';
		$name_high = (string)$stock_code[0].'name_high';
		if ($price_2[$name_low] > $price_1[$name_high]){
			$stock_selected[$i++] = $name;
		}
	}
	return $stock_selected;
}

function Insert_data($stock_selected,$date_array,$begin){
	#echo "BEGIN";
	#echo $date_array[$begin];
	$sql = "delete from analysis where date = '$date_array[$begin]' and reason = 'blank'";
	echo $sql;
	Run_sql($sql);
	$len = sizeof($stock_selected);
	echo "<p style='text-align:center;color:#ddd;font-size:20px'>共 $len 支股票</p>";
	#echo $len;
	#echo "<br>";
	while ($len-- > 0){
		#echo $date_array[$begin];
		$sql = "insert into analysis (name, date, reason) values ('$stock_selected[$len]', '$date_array[$begin]', 'blank')";
		$res = Run_sql($sql);
		if (preg_match("/\(+\w*\W+\w*\)+/", $stock_selected[$len], $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$string = substr($stock_code[0], 1, 2).substr($stock_code[0], 4, -1);
		$url = 'xueqiu.com/S/'.$string;
		echo "<li class='show'><a href='http://$url' style='color:#ddd'>$stock_selected[$len]</a></li>";	
	}
}

$date_array = Get_date($day, $today);
#print_r($date_array);

$len = sizeof($date_array);
while ($len-- > 0){
	$price[$len] = Get_price($date_array[$len]);
}
#print_r($price[0]);

$stock_selected = Is_blank($price[1],$price[0],$date_array[0]);

Insert_data($stock_selected,$date_array,$begin);

?>
</body>
</html>