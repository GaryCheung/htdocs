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
	</style>
</head>

<body bgcolor="#32425c">
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">刺透形态股票</h1>

	<div class="wrapper">
		<a href="/showdata/showall.php" style="text-align:center;color:#ddd">首页</a>
	</div>

<?php

########### 参数区  ###############

$factor_reason = ['chosen','amplitude','goldx','low_quantity_drop','max_quantity','gold5_10x','citou'];
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
	echo $len_profit;
	print_seperation();
	echo $len_safe;
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
	$res = Run_sql($sql);
	$i = 0;
	while ($row = mysql_fetch_row($res)){
		$stock[$i++] = $row[0];
	}
	return $stock;
}

function Score($weight,$today,$stock){
	$len = sizeof($stock);
	echo $len;
	while ($len-- > 0){
		$name = $stock[$len];
		echo $name;
		if (preg_match("/\(+\w*\W+\w*\)+/", $name, $stock_code)){
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
		};
		$code = $stock_code[0];
		$sql = "select * from analysis where date >= '$today' and name like '$code'";
		$res = Run_sql($sql);
		$score[$name] = 1;
		while ($row = mysql_fetch_row($res)){
			$reason = $row[3];
			$score[$name] = $score[$name] * $weight[$reason];
		}
	}
	return $score;
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

$stock = Diff_stock($today);
print_r($stock);

print_seperation();

$score = Score($weight,$today,$stock);
print_r($score);


?>
</body>
</html>




