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
		text-align: left;
		margin-left: 40%;
		margin-top: 10px;
		width: 100%;
		color:#ddd;
	}
	</style>
</head>

<body bgcolor="#32425c">
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">今日股票得分榜</h1>

	<div class="wrapper">
		<a href="/showdata/showall.php" style="text-align:center;color:#ddd">首页</a>
	</div>

<?php

########### 参数区  ###############

$factor_reason = ['chosen','amplitude','goldx','low_quantity_drop','max_quantity','gold5_10x','citou','tunmo','hongsanbing','blank'];
$period = 15;
$increase = 2.00;
$today = date("Y-m-d");

function print_seperation(){
	echo "<br>";
	echo "##########";
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

function Get_proft_weight($factor_reason,$increase,$period){
	if ($increase > 0 ){
		$len = sizeof($factor_reason);
		while ($len-- > 0){
			$reason = $factor_reason[$len];
			$sql = "select * from factor where factor = '$reason' and period = '$period' and increase = '$increase' order by date desc limit 1";
			$res = Run_sql($sql);
			while ($row = mysql_fetch_row($res)){
				$weight[$reason] = $row[3];
			}
		}
	}	
	return $weight;
}

function Get_safe_weight($factor_reason,$period){
	$len = sizeof($factor_reason);
	while ($len-- > 0){
		$reason = $factor_reason[$len];
		$sql = "select * from factor where factor = '$reason' and period = '$period' and increase = 0 order by date desc limit 1";
		$res = Run_sql($sql);
		while ($row = mysql_fetch_row($res)){
			$weight[$reason] = $row[5];
			$weight[$reason] = $weight[$reason]/$period;
		}
	}	
	return $weight;
}

function Get_weight($profit_weight,$safe_weight,$factor_reason){
	$len_profit = sizeof($profit_weight);
	$len_safe = sizeof($safe_weight);
	#echo $len_profit;
	#print_seperation();
	#echo $len_safe;
	if ($len_profit <= $len_safe){
		while ($len_profit-- > 0){
			$reason = $factor_reason[$len_profit];
			$weight[$reason] = $profit_weight[$reason]*$safe_weight[$reason];
		}
	}else if ($len_profit > $len_safe){
		while ($len_safe-- > 0){
			$reason = $factor_reason[$len_safe];
			$weight[$reason] = $profit_weight[$reason]*$safe_weight[$reason];
		}
	}
	return $weight;
}

function Diff_stock($today){
	$sql = "select distinct(name) from analysis where date >= '$today'";
	#echo $sql;
	$res = Run_sql($sql);
	$i = 0;
	while ($row = mysql_fetch_row($res)){
		#print_r($row);
		$stock[$i++] = $row[0];
	}
	return $stock;
}

function Score($weight,$today,$stock){
	$len = sizeof($stock);
	echo $len;
	while ($len-- > 0){
		$name = $stock[$len];
		#echo $name;
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code)){
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
		};
		$code = $stock_code[0];
		$sql = "select * from analysis where date >= '$today' and name like '%$code%'";
		#echo $sql;
		#print_seperation();
		$res = Run_sql($sql);
		$score[$name] = 1;
		while ($row = mysql_fetch_row($res)){
			#print_r($row);
			#print_seperation();
			$reason = $row[3];
			$score[$name] = $score[$name] * $weight[$reason];
		}
	}
	return $score;
}

function Insert_data($score,$today){
	#$sql = "delete from analysis where date >= '$today'";
	#echo $sql;
	#Run_sql($sql);
	$score_out = array_slice($score, 0, 100);
	$len = sizeof($score_out);
	echo "<p style='text-align:center;color:#ddd;font-size:20px'>共 $len 支股票</p>";
	#echo $len;
	#echo "<br>";
	foreach ($score_out as $key => $value) {
		#$sql = "insert into score (name, date, score) values ('$key', '$today', $value)";
		#$res = Run_sql($sql);
		if (preg_match("/\(+\w*\W+\w*\)+/", $key, $stock_code))
			{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		$string = substr($stock_code[0], 1, 2).substr($stock_code[0], 4, -1);
		$url = 'xueqiu.com/S/'.$string;
		echo "<li class='show'><a href='http://$url' style='color:#ddd'>$key 得分 $value</a></li>";	
	}
	$sql = "delete from score where date = '$today'";
	echo $sql; 
	Run_sql($sql);
	foreach ($score as $key => $value) {
		$sql = "insert into score (name, date, score) values ('$key', '$today', '$value')";
		#echo $sql;
		$res = Run_sql($sql);
	}
}

function Analysis($score,$today){
	$sql = "delete from analysis where date = '$today' and reason = 'score100'";
	echo $sql;
	Run_sql($sql);
	$score_out = array_slice($score, 0, 100);
	foreach ($score_out as $key => $value) {
		$sql = "insert into analysis (name, date, reason) values ('$key', '$today', 'score100')";
		$res = Run_sql($sql);
	}
}

$profit_weight = Get_proft_weight($factor_reason,$increase,$period);
#print_r($profit_weight);

#print_seperation();

$safe_weight = Get_safe_weight($factor_reason,$period);
#print_r($safe_weight);

#print_seperation();

$weight = Get_weight($profit_weight,$safe_weight,$factor_reason);
#print_r($weight);

#print_seperation();
#echo "$today";
$stock = Diff_stock($today);
#print_r($stock);

#print_seperation();

$score = Score($weight,$today,$stock);
#print_r($score);
arsort($score);
#print_r($score);
$len = sizeof($score);
echo "<br>";
echo $len;
echo "<br>";

Insert_data($score,$today);

Analysis($score,$today);


?>
</body>
</html>




