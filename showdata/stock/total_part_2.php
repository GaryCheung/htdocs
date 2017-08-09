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
error_reporting(E_ALL || ~E_NOTICE);
set_time_limit(0);

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
		$name_name = (string)$stock_code[0].'name';
		$stock_data[$name_amplitude] = $row[2];
		$stock_data[$name_date] = $row[4];
		$stock_data[$name_open] = $row[6];
		$stock_data[$name_close] = $row[7];
		$stock_data[$name_low] = $row[9];
		$stock_data[$name_high] = $row[8];
		$stock_data[$name_quantity] = $row[3];
		$stock_data[$name_name] = $row[1];
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
			/*
			echo $key;
			echo "========";
			echo $value;
			echo "========";
			echo $average_5_today[$key];
			echo "========";
			echo $average_5_open_today[$key];
			Change_line();
			*/
			$result[$key] = $value;
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
			$stock[$name_close] = $row[7];
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

function Position($today,$days,$list){
	$price_min = Stock_array_default($list);
	$price_max = Stock_array_default($list);
	foreach ($list as $value) {
		/*
		echo $value;
		Change_line();
		echo "$$$$$$$$$$$$$  PRICE MIN   $$$$$$$$$$$$$$$$$$$$";
		Change_line();
		print_r($price_min);
		Change_line();
		*/
		$sql = "select * from `stock_data` where stock_name like '%$value%' order by date DESC limit $days";
		#echo $sql;
		#Change_line();
		$res = Run_sql($sql);
		while($row = mysql_fetch_row($res)){
			/*
			echo "@@@@@@@@@@@@@@@   PRICE MIN  @@@@@@@@@@@@@@@@@";
			echo $price_min[$value];
			Change_line();
			echo "@@@@@@@@@@@@@@@   ROW[7]   @@@@@@@@@@@@@@@@@@@@";
			echo $row[7];
			Change_line();
			
			echo "@@@@@@@@@@@@@@@   STOCK   @@@@@@@@@@@@@@@@@@@@";
			echo $row[1];
			Change_line();
			*/
			if ($price_min[$value] > $row[9] || $price_min[$value] == 0){
				if ($row[9] != 0){
					/*
					echo "################  FIND ONE  ##################";
					Change_line();
					echo "################  ROW[7]  ##################";
					echo $row[7];
					Change_line();
					echo "################  PRICE MIN  ##################";
					echo $price_min[$value];
					Change_line();
					*/
					$price_min[$value] = $row[9];
				}
			}
			if ($price_max[$value] < $row[8] || $price_max[$value] == 0){
				$price_max[$value] = $row[8];
			}
		}
	}
	$today_price = Get_all_stock_data($today);
	/*
	echo "$$$$$$$$$$$$$  TODAY PRICE   $$$$$$$$$$$$$$$$$$$$";
	Change_line();
	print_r($today_price);
	echo "$$$$$$$$$$$$$  PRICE MIN   $$$$$$$$$$$$$$$$$$$$";
	Change_line();
	print_r($price_min);
	Change_line();
	*/
	foreach ($price_min as $key => $value) {
		/*
		echo "^^^^^^^^^^^^^^^^^^^^^^^^^^";
		echo $key;
		Change_line();
		*/
		$name_close = (string)$key.'name_close';
		/*
		echo $name_close;
		Change_line();
		echo $today_price[$name_close];
		Change_line();
		echo "PRICE MIN";
		echo $price_min[$key];
		Change_line();
		*/
		$price[0][$key] = $today_price[$name_close] / $price_min[$key];
		$price[1][$key] = $today_price[$name_close];
		$price[2][$key] = $price_max[$key];
	}
	return $price;	
}

function Price_increase_today($list, $date_array){
	$stock_today = Get_all_stock_data($date_array[0]);
	#print_r($stock_today);
	#Change_line();
	foreach ($list as $value) {
		$name_open = (string)$value.'name_open';

		#echo "################   NAME  OPEN   #################";
		#echo $name_open;
		#Change_line();
		$name_close = (string)$value.'name_close';
		#echo "################   NAME  CLOSE   ################";
		#echo $name_close;
		#Change_line();
		$price_increase_range[$value] = ($stock_today[$name_close] / $stock_today[$name_open] - 1) * 100;
	}
	return $price_increase_range;
}

function Price_increase_range($increase_range){
	$price_increase_range = array('跌停' => 0, '-10 ~ -9' => 0, '-9 ~ -8' => 0, '-8 ~ -7' => 0, '-7 ~ -6' => 0, '-6 ~ -5' => 0, '-5 ~ -4' => 0, '-4 ~ -3' => 0, '-3 ~ -2' => 0, '-2 ~ -1' => 0, '-1 ~ 0' => 0, '0 ~ 1' => 0, '1 ~ 2' => 0, '2 ~ 3' => 0, '3 ~ 4' => 0, '4 ~ 5' => 0, '5 ~ 6' => 0, '6 ~ 7' => 0, '7 ~ 8' => 0, '8 ~ 9' => 0, '9 ~ 10' => 0, '涨停' => 0);
	#print_r($price_increase_range);
	
	foreach ($increase_range as $key => $value){
		if ($value < -9.90){
			$price_increase_range['跌停']++;
		}else if ($value >= -9.90 && $value < -9){
			$price_increase_range['-10 ~ -9']++;
		}else if ($value >= -9 && $value < -8){
			$price_increase_range['-9 ~ -8']++;
		}else if ($value >= -8 && $value <-7){
			$price_increase_range['-8 ~ -7']++;
		}else if ($value >= -7 && $value < -6){
			$price_increase_range['-7 ~ -6']++;			
		}else if ($value >= -6 && $value < -5){
			$price_increase_range['-6 ~ -5']++;
		}else if ($value >= -5 && $value < -4){
			$price_increase_range['-5 ~ -4']++;
		}else if ($value >= -4 && $value < -3){
			$price_increase_range['-4 ~ -3']++;
		}else if ($value >= -3 && $value < -2){
			$price_increase_range['-3 ~ -2']++;
		}else if ($value >= -2 && $value < -1){
			$price_increase_range['-2 ~ -1']++;
		}else if ($value >= -1 && $value < 0){
			$price_increase_range['-1 ~ 0']++;
		}else if ($value >= 0 && $value < 1){
			$price_increase_range['0 ~ 1']++;
		}else if ($value >= 1 && $value < 2){
			$price_increase_range['1 ~ 2']++;
		}else if ($value >= 2 && $value < 3){
			$price_increase_range['2 ~ 3']++;
		}else if ($value >= 3 && $value < 4){
			$price_increase_range['3 ~ 4']++;
		}else if ($value >= 4 && $value < 5){
			$price_increase_range['4 ~ 5']++;
		}else if ($value >= 5 && $value < 6){
			$price_increase_range['5 ~ 6']++;
		}else if ($value >= 6 && $value < 7){
			$price_increase_range['6 ~ 7']++;
		}else if ($value >= 7 && $value < 8){
			$price_increase_range['7 ~ 8']++;
		}else if ($value >= 8 && $value < 9){
			$price_increase_range['8 ~ 9']++;
		}else if ($value >= 9 && $value < 9.90){
			$price_increase_range['9 ~ 10']++;
		}else if ($value >= 9.90){
			$price_increase_range['涨停']++;
		}
	}
	foreach ($price_increase_range as $key => $value) {
		echo "<br>";
		echo $key;
		echo "  ------------------>";
		echo $value;
	}
	return $price_increase_range;
}

function Statistic_increase_range($price_increase_range){
	$total = 0;
	foreach ($price_increase_range as $key => $value) {
		$total += $value; 
	}
	foreach ($price_increase_range as $key => $value) {
		$price_increase_range[$key] = round($value / $total * 100,1);
	}
	foreach ($price_increase_range as $key => $value) {
		/*
		echo "--------------       ";
		echo $key;
		echo "       ------------->";
		echo $value;
		echo "<br>";
		*/
	}
	return $price_increase_range;
}

function Drop_50($price_max, $today_price){
	foreach ($price_max as $key => $value) {
		$name_close = (string)$key.'name_close';
		if ($today_price[$name_close] / $value <= 0.5){
			$drop_50[$key] = $today_price[$name_close] / $value;
		}
	}
	return $drop_50;
}

function Performance_today($today, $list){
	$stock_today = Get_all_stock_data($today);
	$i = 0;
	$total = 0;
	foreach ($list as $value) {
		$name_open = (string)$value.'name_open';
		$name_close = (string)$value.'name_close';
		$total += ($stock_today[$name_close] / $stock_today[$name_open] - 1);
		$i++;
	}
	$performance = $total / $i * 100;
	return $performance;
}

function Above_total_performance($total_performance, $today, $list){
	echo "#########   TOTAL PERFORMANCE   ###########";
	Change_line();
	echo $total_performance;
	Change_line();
	$stock_today = Get_all_stock_data($today);
	foreach ($list as $value) {
		$name_open = (string)$value.'name_open';
		$name_close = (string)$value.'name_close';
		if ((($stock_today[$name_close] / $stock_today[$name_open] - 1)*100) > $total_performance){
			$result[$value] = ($stock_today[$name_close] / $stock_today[$name_open] - 1) * 100;
			/*
			echo "##############     ";
			echo $result[$value];
			echo "    ##################";
			Change_line();
			echo $total_performance;
			Change_line();
			*/
		}
	}
	return $result;
}

function Above_5_above_total($stock_above_5_average, $above_total_performance){
	foreach ($above_total_performance as $key => $value) {
		if ($stock_above_5_average[$key] == $key){
			$result[$key] = $key;
		}
	}
	return $result;
}

function Above_5_above_total_increase($above_5_above_total, $list, $today){
	$stock_today = Get_all_stock_data($today);
	foreach ($list as $value) {
		$name_open = (string)$value.'name_open';
		$name_close = (string)$value.'name_close';
		$increase[$value] = $stock_today[$name_close] / $stock_today[$name_open] - 1;
	}
	#print_r($increase);
	foreach ($above_5_above_total as $key => $value) {
		if ($increase[$key] > 0){
			$result[$key] = $increase[$key];
		}
	}
	return $result;
}

function Lowest_increase_quantity($stock_position, $price_increase_today, $max_quantity){
	foreach ($stock_position as $key => $value) {
		if ($value >= 1 && $value < 1.1 && $price_increase_today[$key] > 0 && $max_quantity[$key] > 0){
			$result[$key] = $key;
		}
	}
	return $result;
}

function Lowest_citou_tunmo($stock_position, $citou, $tunmo){
	foreach ($stock_position as $key => $value) {
		/*
		echo "#######   citou   #############";
		echo $citou[$key];
		Change_line();
		echo "#######   tunmo   #############";
		echo $tunmo[$key];
		Change_line();
		*/
		if ($value >= 1 && $value < 1.1 && $citou[$key] && $tunmo[$key]){
			$result[$key] = $key;
		}
	}
	return $result;
}

/*
function Increase_day_by_day_3($today, $list){
	$days_array = Get_date(3, $today);
	$len_date = sizeof($days_array)--;
	$stock_today = Get_all_stock_data($days_array[0]);
	$stock_yesterday = Get_all_stock_data($days_array[1]);
	$stock_before_yesterday = Get_all_stock_data($days_array[2]);
	$count_array = Stock_array_default($list);
	foreach ($list as $value) {
		$name_quantity = (string)$value.'name_quantity';
		$name_name = (string)$value.'name';
		if ($stock_yesterday[$name_quantity] > 0 && $stock_today[$name_quantity] > 0){
			$count_array[$value]++;
			if ($stock_today[$name_quantity] > $stock_yesterday[$name_quantity]){
				$day_by_day[$value] = 1;
			}
		}
		if ($stock_yesterday[$name_quantity] > 0 && $stock_before_yesterday[$name_quantity] > 0){
			$count_array[$value]++;
			if ($stock_yesterday[$name_quantity] > $stock_before_yesterday[$name_quantity]){
				$day_by_day[$value] = $stock_today[$name_name];
			}
		}
	}
	foreach ($count_array as $key => $value) {
		if ($value == 1){
			$remain = 1;
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
			}
			$name_quantity = (string)$key.'name_quantity';
			if ($stock_yesterday[$name_quantity] > $row[3]){
				$value++;
				$day_by_day[$key] = $stock_today[$name_name];
			}
		}
	}
	return $day_by_day;
}

function Is_3_red_day_by_day($is_3_red, $today){
	foreach ($is_3_red as $key => $value) {
		$sql = "select * from `stock_data` where stock_name like '%$key%' and date <= '$today' order by date DESC limit 3";
		echo $sql;
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

		}
	}
}
*/

function Insert_data_analysis($stock_analysis, $reason, $date_array, $begin){
	$sql = "delete from analysis where date = '$date_array[$begin]' and reason = '$reason'";
	echo $sql;
	Run_sql($sql);
	$sql = "insert into analysis (name, date, reason) values ($stock_analysis, '$date_array[$begin]', '$reason')";
	echo $sql;
	$res = Run_sql($sql);
}


function Insert_data_range($price_increase_statistic, $date_array, $begin){
	$sql = "delete from stock_range where date = '$date_array[$begin]'";
	echo $sql;
	Run_sql($sql);
	Change_line();

	#echo "##############  MIN   ##############";
	#echo $price_increase_statistic['跌停'];
	$i = 0;
	foreach ($price_increase_statistic as $key => $value) {
		$name[$i++] = $value;
	}
	#print_r($name);
	Change_line();
	#$sql = "insert into stock_range (date, MIN) values ('$date_array[$begin]', $name[0])";
	$sql = "insert into stock_range (date, MIN, MINUS_NINE, MINUS_EIGHT, MINUS_SEVEN, MINUS_SIX, MINUS_FIVE, MINUS_FOUR, MINUS_THREE, MINUS_TWO, MINUS_ONE, MINUS_ZERO, ZERO, ONE, TWO, THREE, FOUR, FIVE, SIX, SEVEN, EIGHT, NINE, MAX) values ('$date_array[$begin]', $name[0], $name[1], $name[2], $name[3], $name[4], $name[5], $name[6], $name[7], $name[8], $name[9], $name[10], $name[11], $name[12], $name[13], $name[14], $name[15], $name[16], $name[17], $name[18], $name[19], $name[20], $name[21])";
	echo $sql;
	Change_line();
	$res = Run_sql($sql);
}


function Insert_data_extend($price_level, $price_min, $date_array, $begin, $flag){
	#echo "BEGIN";
	#echo $date_array[$begin];
	$sql = "delete from stock_extend where date = '$date_array[$begin]' and flag = '$flag'";
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
	}
	foreach ($price_min as $key => $value) {
		/*
		echo "#################   KEY  ################# ";
		echo $key;
		Change_line();
		echo "#################   VALUE  ################# ";
		echo $value;
		Change_line();	
		*/	
		$insert_data_name[$key] = $name_list[$key]; 
		$insert_data_lowest[$key] = $price_min[$key];
		$insert_data_level[$key] = $price_level[$key];
	}
	/*
	echo "#################  STOCK NAME   #################";
	print_r($insert_data_name);
	Change_line();
	echo "#################  STOCK LOWEST   #################";
	print_r($insert_data_lowest);
	Change_line();
	echo "#################  STOCK LEVEL   #################";
	print_r($insert_data_level);
	Change_line();	
	*/
	foreach ($insert_data_name as $key => $value) {
		$sql = "insert into stock_extend (stock_name, date, history_lowest_price, price_level, flag) values ('$value', '$date_array[$begin]', '$insert_data_lowest[$key]', '$insert_data_level[$key]', '$flag')";
		#echo $sql;
		$res = Run_sql($sql);
	}
	
}

/*
function Position($date){
	$len_date = sizeof($date);
	$len_date--;
	$stock_array = Stock_array_default($list);
	$sql = "select * from `stock_data` where date <= '$date[0]' and date >= '$date[$len_date]' and source = 'xueqiu' order by date DESC";
	echo $sql;
	Change_line();
	$res = Run_sql($sql);
	$i = 0;
	while($row = mysql_fetch_row($res)){
		echo "Run time -------->";
		echo $i++;
		Change_line();
		#print_r($row);
		$name = $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$name_code = $stock_code[0];
		if ($stock_array[$name_code] > $row[7] || $stock_array[$name_code] == 0){
			if ($row[7] > 0){
				$stock_array[$name_code] = $row[7];	
			}
		}
	}
	$stock_today = Get_all_stock_data($date[0]);
	foreach ($stock_array as $key => $value) {
		$name_close = (string)$key.'name_close';
		echo $name_close;
		Change_line();
		$stock[0][$key] = $stock_today[$name_close] / $value;
		$stock[1][$key] = $stock_today[$name_close]; 
	}
	return $stock;
}
*/

function Insert_data($result,$date_array,$begin,$reason){
	#print_r($result);
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
			/*
			echo "##############    KEY    ################";
			echo $key;
			Change_line();
			echo "##############    NAME CODE    ################";
			echo $name_code;
			Change_line();
			echo "##############    VALUE    ################";
			echo $value;
			Change_line();
			*/
			if ($key == $name_code){
				$insert_data[$key] = $value; 
				#echo "###########   FIND ONE   ##############";
			}
		}
	}
	/*
	Change_line();
	echo "#############  INSERT DATA   #################";
	print_r($insert_data);
	*/
	foreach ($insert_data as $key => $value) {
		$sql = "insert into new_stock_performance (stock_name, value, reason, date) values ('$key', '$value', '$reason', '$date_array[$begin]')";
		#echo $sql;
		#Change_line();
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

$today_array = Get_date(1,$today);
$today = $today_array[0];

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

Insert_data($max_amplitude, $date_array, $begin, 'max_amplitude_10_days');

echo "Rule 1 ---> max_amplitude in 10 days"; 
Change_line();
echo "------>  Result Number ::";
echo sizeof($max_amplitude);
Change_line();


#################   收盘价高于5日均线  ###################
$date_array = Get_date(5,$today);
#print_r($date_array);

$average_5_today = Average_5($date_array,$list);
#print_r($average_5_today);

$stock_above_5_average = Above_5_average($average_5_today,$date_array);
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

$date_array_yesterday_5_average = Get_date(5,$yesterday);
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
$is_citou = Is_citou($at_bottom,$today,$stock_today);
#print_r($is_citou);
#Change_line();

Insert_data($is_citou,$date_array,0,'citou');

echo "Rule 6 ---> At 20-day bottom & citou"; 
Change_line();
echo "------>  Result Number ::";
echo sizeof($is_citou);
Change_line();


#################   底部刺透形态    ########################
$is_tunmo = Is_tunmo($at_bottom,$today,$stock_today);
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
Change_line();

Insert_data($is_3_red,$date_array,0,'3_red');

echo "Rule 8 ---> 3_red"; 
Change_line();
echo "------>  Result Number ::";
echo sizeof($is_3_red);
Change_line();


#################   跳空上涨形态    ########################
$date_blank_increase = Get_date(2,$today);

$blank_increase = Blank_increase($date_blank_increase);
#print_r($blank_increase);
#Change_line();

Insert_data($blank_increase,$date_array,0,'blank_increase');

echo "Rule 9 ---> blank_increase"; 
Change_line();
echo "------>  Result Number ::";
echo sizeof($blank_increase);
Change_line();

*/
#################   价格位置    ########################
$days_price_level = 100;     # 近100天价格位置

$price_level = Position($today,$days_price_level,$list);

echo "#################   股票总数    #######################";
Change_line(); 
echo sizeof($list);
Change_line();


#echo "#################   价格位置    #######################";
#Change_line(); 
#print_r($price_level[0]);
#Change_line();
#echo "#################   收盘价格    #######################";
#print_r($price_level[1]);
#Change_line();

Insert_data_extend($price_level[0], $price_level[1], [$today], 0, 'new');

echo "Rule 10 ---> stock_position"; 
Change_line();
echo "------>  Result Number ::";
echo sizeof($price_level[0]);
Change_line();


#################   今日价格涨幅分布    ########################
$date_array_price_range = Get_date(1,$today);

$price_increase_today = Price_increase_today($list, $date_array_price_range);
#print_r($price_increase_today);
#Change_line();

$price_increase_range = Price_increase_range($price_increase_today);
#print_r($price_increase_range);
#Change_line();

Change_line();
echo "Rule 11 ---> stock increase range"; 
Change_line();

$price_increase_statistic = Statistic_increase_range($price_increase_range);
#print_r($price_increase_statistic);
#Change_line();

Insert_data_range($price_increase_statistic, $date_array_price_range, 0);


#################   最高价跌幅超过50%    ########################
$price_max = $price_level[2];
#echo "############   PRICE MAX  ################";
#print_r($price_max);
#Change_line();

$price_today = Get_all_stock_data($today);
#echo "############   PRICE TODAY    ############";
#print_r($price_today);
#Change_line();

$drop_50 = Drop_50($price_max, $price_today);
#print_r($drop_50);

Insert_data($drop_50, $date_array, 0, 'drop_50');

echo "Rule 12 ---> drop over 50%"; 
Change_line();
echo "------>  Result Number ::";
echo sizeof($drop_50);
Change_line();


#################   今日所有股票平均涨幅    ########################
$performance_today = Performance_today($today, $list);
#print_r($performance_today);

Insert_data_analysis($performance_today, 'total_new', $date_array, $begin);

Change_line();
echo "Rule 13 ---> Today total_stock performance"; 
Change_line();
echo "------>  Result ::";
echo $performance_today;
Change_line();


#################   今日涨幅 高于 全部股票加权涨幅    ########################
$above_total_performance = Above_total_performance($performance_today, $today, $list);
#print_r($above_total_performance);

Insert_data($above_total_performance, $date_array, $begin, 'above_total');

echo "Rule 14 ---> Above total average performance"; 
Change_line();
echo "------>  Result Number ::";
echo sizeof($above_total_performance);
Change_line();


echo "<br>";
?>

<embed height="100" width="100" src="glqxz.mp3" />

</body>
</html>











