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
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">运行常规程序</h1>

	<div class="wrapper">
		<a href="/showdata/showall.php" style="text-align:left;color:#ddd">首页</a>
	</div>


<?php

############ 参数区  ###############
$period = 10;
$begin = 0;
#echo $begin;
$today = date("Y-m-d");
$yesterday = date("Y-m-d",strtotime("-1 day"));
$before_yesterday = date("Y-m-d",strtotime("-2 day"));
#$today = '2017-01-26';
$days = 2;      ####### 最近days天 ########
$days_amplitude = 10;
$days_quantity = 10;
$days_10_average = 10;
$days_citou = 20;

function Change_line(){
	echo "<br>";
	echo "~~~~~~~~~~~~~";
	echo "<br>";
}

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
	echo $sql;
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

function Stock_array_default($list){
	$len = sizeof($list);
	while ($len--){
		$stock_code = $list[$len];
		$stock_array[$stock_code] = 0;  
	}
	return $stock_array;
}

function Get_all_stock_data($date){
	$sql="select * from `stock_data` where date = '$date' and source = 'xueqiu'";
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
		$name_amplitude = (string)$stock_code[0].'name_amplitude';
		$name_date = (string)$stock_code[0].'name_date';
		$name_open = (string)$stock_code[0].'name_open';
		$name_close = (string)$stock_code[0].'name_close';
		$name_low = (string)$stock_code[0].'name_low';
		$name_high = (string)$stock_code[0].'name_high';
		$name_quantity = (string)$stock_code[0].'name_quantity';

		$stock_data[$name_amplitude] = $row[2];
		$stock_data[$name_date] = $row[4];
		$stock_data[$name_open] = $row[6];
		$stock_data[$name_close] = $row[7];
		$stock_data[$name_low] = $row[9];
		$stock_data[$name_high] = $row[8];
		$stock_data[$name_quantity] = $row[3];
	}
	return $stock_data;		
}

function Get_min_quantity_drop($days_quantity,$date,$list){
	$len_date = sizeof($date);
	$len_date--;
	$i = 0;
	$stock_array = Stock_array_default($list);
	$count_array = Stock_array_default($list);
	$sql = "select * from `stock_data` where date <= '$date[0]' and date >= '$date[$len_date]' and source = 'xueqiu'";
	echo $sql;
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		#echo $i++;
		#Change_line();
		#print_r($row);
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name_code = $stock_code[0];
		$count_array[$name_code]++;
		if ($stock_array[$name_code] > $row[3]){
			$stock_array[$name_code] = $row[3];
			$min_quantity_date[$name_code] = $row[4];
			$min_quantity_price_open[$name_code] = $row[6];
			$min_quantity_price_close[$name_code] = $row[7];
		}else if($stock_array[$name_code] == 0){
			$stock_array[$name_code] = $row[3];
			$min_quantity_date[$name_code] = $row[4];
			$min_quantity_price_open[$name_code] = $row[6];
			$min_quantity_price_close[$name_code] = $row[7];
		}
	}

	$i = 0;
	foreach ($count_array as $key => $value) {
		if ($value < $days_quantity){
			$sql = "select * from `stock_data` where stock_name like '%$key%' and date <= '$date[0]' and source = 'xueqiu' order by date DESC limit $days_quantity";
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
				$name_code = $stock_code[0];
				if ($stock_array[$name_code] > $row[3]){
					$stock_array[$name_code] = $row[3];
					$min_quantity_date[$name_code] = $row[4];
					$min_quantity_price_open[$name_code] = $row[6];
					$min_quantity_price_close[$name_code] = $row[7];
				}
			}
		}
	}
		/*
		echo "############  FINISHED";
		echo "-->";
		echo $i++;
		echo "    #################";
		Change_line();
		*/
	#print_r($max_quantity_date);
	#echo $date[0];
	#Change_line();
	foreach ($min_quantity_date as $key => $value) {
		if ($value == $date[0] && $min_quantity_price_open[$key]>$min_quantity_price_close[$key]){
			$result[$key] = $stock_array[$key];
		}
	}

	#print_r($result);
	return $result;
}

function Get_max_quantity($days_quantity,$date,$list){
	$len_date = sizeof($date);
	$len_date--;
	$i = 0;
	$stock_array = Stock_array_default($list);
	$count_array = Stock_array_default($list);
	$sql = "select * from `stock_data` where date <= '$date[0]' and date >= '$date[$len_date]' and source = 'xueqiu'";
	echo $sql;
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		#echo $i++;
		#Change_line();
		#print_r($row);
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name_code = $stock_code[0];
		$count_array[$name_code]++;
		if ($stock_array[$name_code] < $row[3]){
			$stock_array[$name_code] = $row[3];
			$max_quantity_date[$name_code] = $row[4];
		}
	}

	$i = 0;
	foreach ($count_array as $key => $value) {
		if ($value < $days_quantity){
			$sql = "select * from `stock_data` where stock_name like '%$key%' and date <= '$date[0]' and source = 'xueqiu' order by date DESC limit $days_quantity";
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
				$name_code = $stock_code[0];
				if ($stock_array[$name_code] < $row[3]){
					$stock_array[$name_code] = $row[3];
					$max_quantity_date[$name_code] = $row[4];
				}
			}
		}
	}
		/*
		echo "############  FINISHED";
		echo "-->";
		echo $i++;
		echo "    #################";
		Change_line();
		*/
	#print_r($max_quantity_date);
	#echo $date[0];
	#Change_line();
	foreach ($max_quantity_date as $key => $value) {
		if ($value == $date[0]){
			$result[$key] = $stock_array[$key];
		}
	}
	#print_r($result);
	return $result;
}

function Get_max_amplitude($days_amplitude,$date,$list){
	$len_date = sizeof($date);
	$len_date--;
	$i = 0;
	$stock_array = Stock_array_default($list);
	$count_array = Stock_array_default($list);
	$sql = "select * from `stock_data` where date <= '$date[0]' and date >= '$date[$len_date]' and source = 'xueqiu'";
	echo $sql;
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		#echo $i++;
		#Change_line();
		#print_r($row);
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name_code = $stock_code[0];
		$count_array[$name_code]++;
		if ($stock_array[$name_code] < $row[2]){
			$stock_array[$name_code] = $row[2];
			$max_amplitude_date[$name_code] = $row[4];
		}
	}
	$i = 0;
	foreach ($count_array as $key => $value) {
		if ($value < $days_amplitude){
			/*
			Change_line();
			echo $key;
			echo "------>";
			echo $value;
			Change_line();
			*/
			$sql = "select * from `stock_data` where stock_name like '%$key%' and date <= '$date[0]' and source = 'xueqiu' order by date DESC limit $days_amplitude";
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
				$name_code = $stock_code[0];
				if ($stock_array[$name_code] < $row[2]){
					$stock_array[$name_code] = $row[2];
					$max_amplitude_date[$name_code] = $row[4];
				}
			}
		}
	}
		/*
		echo "############  FINISHED";
		echo "-->";
		echo $i++;
		echo "    #################";
		Change_line();
		*/
	foreach ($max_amplitude_date as $key => $value) {
		if ($value == $date[0]){
			$result[$key] = $stock_array[$key];
		}
	}
	return $result;
}

function Average_5_cross_10($average_10_today,$average_5_today,$average_5_open_today){
	foreach ($average_10_today as $key => $value) {
		if ($value < $average_5_today[$key] && $value > $average_5_open_today[$key]){
			echo $key;
			echo "========";
			echo $value;
			echo "========";
			echo $average_5_today[$key];
			echo "========";
			echo $average_5_open_today[$key];
			Change_line();
			$result[$key] = $key;
		}
	}
	return $result;
}

function Average_10($date,$list){
	$len_date = sizeof($date);
	$len_date--;
	$stock_array = Stock_array_default($list);
	$count_array = Stock_array_default($list);	
	$sql = "select * from `stock_data` where date <= '$date[0]' and date >= '$date[$len_date]' and source = 'xueqiu'";
	echo $sql;
	Change_line();
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		#echo $i++;
		#Change_line();
		#print_r($row);
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name_code = $stock_code[0];
		$count_array[$name_code]++;
		$stock_array[$name_code] += $row[7]; 
	}
	#print_r($count_array);
	#Change_line();
	$i = 0;
	foreach ($count_array as $key => $value) {
		if ($value < 10){
			$remain = 10 - $value;
			$sql = "select * from `stock_data` where stock_name like '%$key%' and date < '$date[$len_date]' and source = 'xueqiu' order by date DESC limit $remain";
			#echo $sql;
			#Change_line();
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
				$count_array[$name_code]++;
				$stock_array[$name_code] += $row[7];
			}
		}
		$stock_array[$key] = $stock_array[$key] / 10;
		/*
		echo "############  FINISHED";
		echo "-->";
		echo $i++;
		echo "    #################";
		Change_line();
		*/
	}	
	return $stock_array;
}

function Average_5_open($date,$list){
	$len_date = sizeof($date);
	$len_date--;
	$stock_array = Stock_array_default($list);
	$count_array = Stock_array_default($list);	
	$sql = "select * from `stock_data` where date < '$date[0]' and date >= '$date[$len_date]' and source = 'xueqiu'";
	echo $sql;
	Change_line();
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		#echo $i++;
		#Change_line();
		#print_r($row);
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name_code = $stock_code[0];
		$count_array[$name_code]++;
		$stock_array[$name_code] += $row[7]; 
	}
	$sql = "select * from `stock_data` where date = '$date[0]' and date >= '$date[$len_date]' and source = 'xueqiu'";
	echo $sql;
	Change_line();
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		#echo $i++;
		#Change_line();
		#print_r($row);
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name_code = $stock_code[0];
		$count_array[$name_code]++;
		$stock_array[$name_code] += $row[6]; 
	}

	#print_r($count_array);
	#Change_line();
	$i = 0;
	foreach ($count_array as $key => $value) {
		if ($value < 5){
			$remain = 5 - $value;
			$sql = "select * from `stock_data` where stock_name like '%$key%' and date < '$date[$len_date]' and source = 'xueqiu' order by date DESC limit $remain";
			#echo $sql;
			#Change_line();
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
				$count_array[$name_code]++;
				$stock_array[$name_code] += $row[7];
			}
		}
		$stock_array[$key] = $stock_array[$key] / 5;
		/*
		echo "############  FINISHED";
		echo "-->";
		echo $i++;
		echo "    #################";
		Change_line();
		*/
	}	
	return $stock_array;
}

function Average_5($date,$list){
	$len_date = sizeof($date);
	$len_date--;
	$stock_array = Stock_array_default($list);
	$count_array = Stock_array_default($list);	
	$sql = "select * from `stock_data` where date <= '$date[0]' and date >= '$date[$len_date]' and source = 'xueqiu'";
	echo $sql;
	Change_line();
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		#echo $i++;
		#Change_line();
		#print_r($row);
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name_code = $stock_code[0];
		$count_array[$name_code]++;
		$stock_array[$name_code] += $row[7]; 
	}
	#print_r($count_array);
	#Change_line();
	$i = 0;
	foreach ($count_array as $key => $value) {
		if ($value < 5){
			$remain = 5 - $value;
			$sql = "select * from `stock_data` where stock_name like '%$key%' and date < '$date[$len_date]' and source = 'xueqiu' order by date DESC limit $remain";
			#echo $sql;
			#Change_line();
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
				$count_array[$name_code]++;
				$stock_array[$name_code] += $row[7];
			}
		}
		$stock_array[$key] = $stock_array[$key] / 5;
		/*
		echo "############  FINISHED";
		echo "-->";
		echo $i++;
		echo "    #################";
		Change_line();
		*/
	}	
	return $stock_array;
}

function Above_5_average($average_5,$date){
	$sql = "select * from `stock_data` where date = '$date[0]' and source = 'xueqiu'";
	echo $sql;
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		#echo $i++;
		#Change_line();
		#print_r($row);
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name_code = $stock_code[0];
		if ($average_5[$name_code] < $row[7]){
			$name_close = (string)$stock_code[0].'name_close';
			$name_5_average = (string)$stock_code[0].'name_5_average';
			$stock[$name_price] = $row[7];
			$stock[$name_5_average] = $average_5[$name_code];
			$stock[$name_code] = $name_code;
		}
	}
	return $stock;
}

function At_bottom($date,$list){
	$len_date = sizeof($date);
	$len_date--;
	$stock_array = Stock_array_default($list);
	$count_array = Stock_array_default($list);
	$sql = "select * from `stock_data` where date <= '$date[0]' and date >= '$date[$len_date]' and source = 'xueqiu'";
	echo $sql;
	Change_line();
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		#echo $i++;
		#Change_line();
		#print_r($row);
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name_code = $stock_code[0];
		$count_array[$name_code]++;
		if ($stock_array[$name_code] > $row[9] || $stock_array[$name_code] == 0){
			$stock_array[$name_code] = $row[9];
			$bottom_date[$name_code] = $row[4];
		}
	}
	$len = sizeof($date);
	foreach ($count_array as $key => $value) {
		if ($value < $len){
			$remain = $len - $value;
			/*
			Change_line();
			echo $key;
			echo "------>";
			echo $value;
			Change_line();
			*/
			$sql = "select * from `stock_data` where stock_name like '%$key%' and date < '$date[$len_date]' and source = 'xueqiu' order by date DESC limit $remain";
			echo $sql;
			Change_line();
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
				if ($stock_array[$name_code] > $row[9] || $stock_array[$name_code] == 0){
					$stock_array[$name_code] = $row[9];
					$bottom_date[$name_code] = $row[4];
				}

			}
		}
	}	
	return $stock_array;
}

function Is_citou($at_bottom,$yesterday,$stock_today){
	$sql = "select * from `stock_data` where date = '$yesterday' and source = 'xueqiu'";
	echo $sql;
	Change_line();
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		#echo $i++;
		#Change_line();
		#print_r($row);
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name_code = $stock_code[0];
		$name_amplitude = (string)$stock_code[0].'name_amplitude';
		$name_date = (string)$stock_code[0].'name_date';
		$name_open = (string)$stock_code[0].'name_open';
		$name_close = (string)$stock_code[0].'name_close';
		$name_low = (string)$stock_code[0].'name_low';
		$name_high = (string)$stock_code[0].'name_high';
		$name_quantity = (string)$stock_code[0].'name_quantity';
		if ($at_bottom[$name_code] == $row[9] && $row[7] < $row[6] && $stock_today[$name_open] < $stock_today[$name_close] && $stock_today[$name_close] >= $row[6] + 0.5*($row[7] - $row[6])){
			$result[$name_code] = $row[1];
		}
	}
	return $result;
}

function Is_tunmo($at_bottom,$yesterday,$stock_today){
	$sql = "select * from `stock_data` where date = '$yesterday' and source = 'xueqiu'";
	echo $sql;
	Change_line();
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		#echo $i++;
		#Change_line();
		#print_r($row);
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name_code = $stock_code[0];
		$name_amplitude = (string)$stock_code[0].'name_amplitude';
		$name_date = (string)$stock_code[0].'name_date';
		$name_open = (string)$stock_code[0].'name_open';
		$name_close = (string)$stock_code[0].'name_close';
		$name_low = (string)$stock_code[0].'name_low';
		$name_high = (string)$stock_code[0].'name_high';
		$name_quantity = (string)$stock_code[0].'name_quantity';
		if ($at_bottom[$name_code] == $row[9] && $row[7] < $row[6] && $stock_today[$name_open] < $stock_today[$name_close] && $stock_today[$name_close] > $row[6] && $stock_today[$name_open] < $row[7]){
			$result[$name_code] = $row[1];
		}
	}
	return $result;
}

function Is_3_red($date){
	$len_date = sizeof($date);
	$len_date--;
	$stock_array = Stock_array_default($list);
	$count_array = Stock_array_default($list);
	$sql = "select * from `stock_data` where date <= '$date[0]' and date >= '$date[$len_date]' and source = 'xueqiu' order by date DESC";
	echo $sql;
	Change_line();
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		#echo $i++;
		#Change_line();
		#print_r($row);
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name_code = $stock_code[0];
		if ($count_array[$name_code] == 0){
			$stock_3_red_today_close[$name_code] = $row[7];
			$stock_3_red_today_open[$name_code] = $row[6];
			$count_array[$name_code]++;
		}else if ($count_array[$name_code] == 1){
			$stock_3_red_yesterday_close[$name_code] = $row[7];
			$stock_3_red_yesterday_open[$name_code] = $row[6];
			$count_array[$name_code]++;
		}else if ($count_array[$name_code] == 2){
			$stock_3_red_before_close[$name_code] = $row[7];
			$stock_3_red_before_open[$name_code] = $row[6];
			$count_array[$name_code]++;
		}
	}
	foreach ($count_array as $key => $value) {
		if ($value < 3){
			$remain = 3 - $value;
			/*
			Change_line();
			echo $key;
			echo "------>";
			echo $value;
			Change_line();
			*/
			$sql = "select * from `stock_data` where stock_name like '%$key%' and date < '$date[$len_date]' and source = 'xueqiu' order by date DESC limit $remain";
			echo $sql;
			Change_line();
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
				if ($count_array[$name_code] == 0){
					$stock_3_red_today_close[$name_code] = $row[7];
					$stock_3_red_today_open[$name_code] = $row[6];
					$count_array[$name_code]++;
				}else if ($count_array[$name_code] == 1){
					$stock_3_red_yesterday_close[$name_code] = $row[7];
					$stock_3_red_yesterday_open[$name_code] = $row[6];
					$count_array[$name_code]++;
				}else if ($count_array[$name_code] == 2){
					$stock_3_red_before_close[$name_code] = $row[7];
					$stock_3_red_before_open[$name_code] = $row[6];
					$count_array[$name_code]++;
				}
			}
		}
		if ($stock_3_red_today_close[$key] > $stock_3_red_yesterday_close[$key] && $stock_3_red_yesterday_close[$key] > $stock_3_red_before_close[$key] && $stock_3_red_today_open[$key] < $stock_3_red_today_close[$key] && $stock_3_red_yesterday_open[$key] < $stock_3_red_yesterday_close[$key] && $stock_3_red_before_open[$key] < $stock_3_red_before_close[$key] ){
			$result[$key] = $key;
		}
	}
	return $result;
}

function Blank_increase($date){
	$len_date = sizeof($date);
	$len_date--;
	$stock_array = Stock_array_default($list);
	$count_array = Stock_array_default($list);
	$sql = "select * from `stock_data` where date <= '$date[0]' and date >= '$date[$len_date]' and source = 'xueqiu' order by date DESC";
	echo $sql;
	Change_line();
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		#echo $i++;
		#Change_line();
		#print_r($row);
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name_code = $stock_code[0];
		if ($count_array[$name_code] == 0){
			$blank_increase_close[$name_code] = $row[7];
			$blank_increase_open[$name_code] = $row[6];
			$blank_increase_low[$name_code] = $row[9];
			$count_array[$name_code]++;
		}else if ($count_array[$name_code] == 1){
			$blank_increase_yesterday_high[$name_code] = $row[8];
			$count_array[$name_code]++;
		}
	}
	foreach ($count_array as $key => $value) {
		if ($value < 2){
			$remain = 2 - $value;
			/*
			Change_line();
			echo $key;
			echo "------>";
			echo $value;
			Change_line();
			*/
			$sql = "select * from `stock_data` where stock_name like '%$key%' and date < '$date[$len_date]' and source = 'xueqiu' order by date DESC limit $remain";
			echo $sql;
			Change_line();
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
				if ($count_array[$name_code] == 0){
					$blank_increase_close[$name_code] = $row[7];
					$blank_increase_open[$name_code] = $row[6];
					$blank_increase_low[$name_code] = $row[9];
					$count_array[$name_code]++;
				}else if ($count_array[$name_code] == 1){
					$blank_increase_yesterday_high[$name_code] = $row[8];
					$count_array[$name_code]++;
				}
			}
		}
		if ($blank_increase_open[$key] < $blank_increase_close[$key] && $blank_increase_low[$key] > $blank_increase_yesterday_high[$key]){
			$result[$key] = $key;
		}
	}
	return $result;
}

function Insert_data($result,$date_array,$begin,$reason){
	#echo "BEGIN";
	#echo $date_array[$begin];
	$sql = "delete from new_stock_performance where date = '$date_array[$begin]' and reason = '$reason'";
	echo $sql;
	Run_sql($sql);
	#echo "<p style='text-align:center;color:#ddd;font-size:20px'>今日股票加权涨幅 $result</p>";
	#echo $len;
	#echo "<br>";
	$sql = "select * from `stock_data` where date = '$date_array[0]' and source = 'xueqiu'";
	echo $sql;
	Change_line();
	$res = Run_sql($sql);
	while($row = mysql_fetch_row($res)){
		#echo $i++;
		#Change_line();
		#print_r($row);
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name_code = $stock_code[0];
		$name_list[$name_code] = $row[1];
		foreach ($result as $key => $value) {
			if ($key == $name_code){
				$insert_data[$key] = $name_list[$key]; 
			}
		}
	}
	foreach ($insert_data as $key => $value) {
		$sql = "insert into new_stock_performance (stock_name, reason, date) values ('$value', '$reason', '$date_array[$begin]')";
		#echo $sql;
		$res = Run_sql($sql);
	}
	
}

################  初始化参数   #####################
$date_array_10_average = Get_date($days_10_average,$today);
#print_r($date_array);
#Change_line();

$date_array = Get_date($days_amplitude,$today);
#print_r($date_array);
#Change_line();

$list = Stock_list($date_array);
#print_r($list);
#Change_line();

$stock_array = Stock_array_default($list);
#print_r($stock_array);
#Change_line();

$stock_data = Get_all_stock_data($date_array[0]);
#print_r($stock_data);

/*
#############  近10日最大振幅  ####################
$max_amplitude = Get_max_amplitude($days_amplitude,$date_array,$list);
#print_r($max_amplitude);

Insert_data($max_amplitude,$date_array,0,'max_amplitude_10_days');

echo "Rule 1 ---> max_amplitude in 10 days"; 
Change_line();
echo "------>  Result Number ::";
echo sizeof($max_amplitude);
Change_line();

#################   收盘价高于5日均线  ###################
$date_array = Get_date(5,$today);
#print_r($date_array);

$average_5_today = Average_5($date_array,$list);
#print_r($average_5);

$stock_above_5_average = Above_5_average($average_5,$date_array);
#print_r($stock_above_5_average);

Insert_data($stock_above_5_average,$date_array,0,'above_5_average');

echo "Rule 2 ---> Price above 5 average line"; 
Change_line();
echo "------>  Result Number ::";
echo sizeof($stock_above_5_average) / 3;
Change_line();


################  10日最大成交量  ########################
$max_quantity = Get_max_quantity($days_quantity,$date_array,$list);
#print_r($max_quantity);

Insert_data($max_quantity,$date_array,0,'max_quantity_10_days');

echo "Rule 3 ---> max_quantity in 10 days"; 
Change_line();
echo "------>  Result Number ::";
echo sizeof($max_quantity);
Change_line();

#################   10日最小成交量&&下跌  ##################
$min_quantity = Get_min_quantity_drop($days_quantity,$date_array,$list);
#print_r($max_quantity);

Insert_data($min_quantity,$date_array,0,'min_quantity_10_days_drop');

echo "Rule 4 ---> min_quantity in 10 days & drop"; 
Change_line();
echo "------>  Result Number ::";
echo sizeof($min_quantity);
Change_line();


##################   5日均线上穿10日均线   ##################
$date_array_today = Get_date(10,$today);
#print_r($date_array_today);
#Change_line();

$date_array_yesterday_5_average = Get_date(5,$before_yesterday);
#print_r($date_array_today_5_average);
#Change_line();

$date_array_today_5_average = Get_date(5,$today);
#print_r($date_array_today_5_average);
#Change_line();


$average_10_today = Average_10($date_array_today,$list);
#print_r($average_10);

$average_5_yesterday = Average_5($date_array_yesterday_5_average,$list);
#print_r($average_5_yesterday);
Change_line();
$average_5_today = Average_5($date_array_today_5_average,$list);
#print_r($average_5_today);
Change_line();

$average_5_cross_average_10 = Average_5_cross_10($average_10_today,$average_5_today,$average_5_yesterday);
#print_r($average_5_cross_average_10);
#Change_line();

Insert_data($average_5_cross_average_10,$date_array,0,'average_5_cross_average_10');

echo "Rule 5 ---> average_5_cross_average_10"; 
Change_line();
echo "------>  Result Number ::";
echo sizeof($average_5_cross_average_10);
Change_line();


#################   底部刺透形态/吞没形态初始化    ########################
$date_array_citou = Get_date($days_citou,$today);
#print_r($date_array);
#Change_line();

$at_bottom = At_bottom($date_array_citou,$list);
#print_r($at_bottom);
#Change_line();

$stock_today = Get_all_stock_data($date_array_citou[0]);


#################   底部刺透形态    ########################
$is_citou = Is_citou($at_bottom,$before_yesterday,$stock_today);
#print_r($is_citou);
#Change_line();

Insert_data($is_citou,$date_array,0,'citou');

echo "Rule 6 ---> At 20-day bottom & citou"; 
Change_line();
echo "------>  Result Number ::";
echo sizeof($is_citou);
Change_line();

#################   底部刺透形态    ########################
$is_tunmo = Is_tunmo($at_bottom,$before_yesterday,$stock_today);
#print_r($is_tunmo);
#Change_line();

Insert_data($is_tunmo,$date_array,0,'tunmo');

echo "Rule 7 ---> At 20-day bottom & tunmo"; 
Change_line();
echo "------>  Result Number ::";
echo sizeof($is_tunmo);
Change_line();


#################   红三兵形态    ########################
$date_array_3_red = Get_date(3,$today);

$is_3_red = Is_3_red($date_array_3_red);
#print_r($is_3_red);
#Change_line();

Insert_data($is_tunmo,$date_array,0,'3_red');

echo "Rule 8 ---> 3_red"; 
Change_line();
echo "------>  Result Number ::";
echo sizeof($is_3_red);
Change_line();


#################   红三兵形态    ########################
$date_blank_increase = Get_date(2,$today);

$blank_increase = Blank_increase($date_blank_increase);
print_r($blank_increase);
Change_line();

Insert_data($blank_increase,$date_array,0,'blank_increase');

echo "Rule 9 ---> blank_increase"; 
Change_line();
echo "------>  Result Number ::";
echo sizeof($blank_increase);
Change_line();
*/

echo "<br>";
?>

<embed height="100" width="100" src="glqxz.mp3" />

</body>
</html>











