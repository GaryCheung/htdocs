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
#$today = '2017-01-23';
$days = 2;      ####### 最近days天 ########

echo "<p style='text-align:center;color:#ddd;font-size:20px'>昨日出现近 $day 天底部，并发生吞没形态</p>";


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

function All_stock($date_array){
	$sql="select * from `stock_data` where date = '$date_array[0]' and source = 'xueqiu' ";
	#echo $sql;
	echo "<br>";
	$res = Run_sql($sql);
	$flag = 0;
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
		$min_price[$stock_code[0]] = $stock_code[0];
		$min_price[$name_date] = $row[4];
		$min_price[$name_open]=$row[6];
		$min_price[$name_close] = $row[7];
		$min_price[$name_low] = $row[9];
		$flag++;
	}
	echo $flag;
	return $min_price;
}

function At_bottom($date_array,$min_price){
	#print_r($min_price);
	$length = sizeof($date_array);
	#echo $length;
	$i = 1;
	$flag = 0;
	while ($i < $length){
		$sql="select * from `stock_data` where date = '$date_array[$i]' and source = 'xueqiu' ";
		$i++;
		#echo $sql;
		$res = Run_sql($sql);
		while($row = mysql_fetch_row($res)){
			#print_r($row);
			$name = $row[1];
			if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
				{
				#echo "yes";
				#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
				};
			$name = $stock_code[0];
			$name_open = (string)$stock_code[0].'name_open';
			$name_close = (string)$stock_code[0].'name_close';
			$name_date = (string)$stock_code[0].'name_date';
			$price_low = $row[9];
			$name_low = (string)$stock_code[0].'name_low';
			#echo $name_date;
			#echo "<br>";
			/*
			echo "<br>";
			echo "@@@@@@@@@   price_low";
			echo $price_low;
			echo "@@@@@@@@@";
			echo "<br>";
			echo "#############  min_price";
			echo $min_price[$name];
			echo "############";
			
			echo $name;
			echo "#####  min_price  ####";
			echo $min_price[$name];
			echo "<br>";
			*/

			if ($price_low <= $min_price[$name_low]  && $price_low > 0){
				$min_price[$name_low]=$price_low;
				$min_price[$name_date]=$row[4];
				$min_price[$name_open]=$row[6];
				$min_price[$name_close] = $row[7];
				#echo "GET ONE";
			}

		}
	}
	return $min_price;
}

function Is_tunmo($min_price, $cmp_date){
	$i=0;
	$sql="select * from `stock_data` where date = '$cmp_date[0]' and source = 'xueqiu' ";
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
		$price_open = $row[6];
		$price_close = $row[7];
		if ($min_price[$name_date] == $cmp_date[1]){
			#echo "!!!!!!!!  SUCESS  GET  ONE   !!!!!!!!!!!";
			if ($price_open < $min_price[$name_close] && $price_close > $min_price[$name_open] && $price_close > $price_open && $min_price[$name_close] < $min_price[$name_open]){
				$stock_selected[$i++] = $name;
			}
		}
		
	}
	return $stock_selected;
}

function Insert_data($stock_selected,$date_array,$begin){
	#echo "BEGIN";
	#echo $date_array[$begin];
	$sql = "delete from analysis where date >= '$date_array[$begin]' and reason = 'tunmo'";
	echo $sql;
	Run_sql($sql);
	$len = sizeof($stock_selected);
	echo "<p style='text-align:center;color:#ddd;font-size:20px'>共 $len 支股票</p>";
	#echo $len;
	#echo "<br>";
	while ($len-- > 0){
		#echo $date_array[$begin];
		$sql = "insert into analysis (name, date, reason) values ('$stock_selected[$len]', '$date_array[$begin]', 'tunmo')";
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

$date_array = Get_date($day,$today);
#print_r($date_array);
#echo "<br>";



$min_price = All_stock($date_array);
#print_r($min_price);
#echo "<br>";

$bottom_price = At_bottom($date_array,$min_price);
#echo "############  min_price #############";
#echo "<br>";
#print_r($bottom_price);

$list = Stock_list($date_array);
#print_r($list);

$cmp_date = Last_deal_date($today,$days);
#print_r($cmp_date);

/*
$len = sizeof($list);
$flag = 0;
for ($i=0; $i<$len; $i++){
	$name_date = (string)$list[$i].'name_date';
	echo $name_date;
	echo $bottom_price[$name_date];
	echo "<br>";
	if ($bottom_price[$name_date] == $cmp_date[1]){
		$flag++;
	}
}
echo $flag;
*/

$stock_selected = Is_tunmo($bottom_price, $cmp_date);
#print_r($stock_selected);

Insert_data($stock_selected,$date_array,$begin);


?>
</body>
</html>
