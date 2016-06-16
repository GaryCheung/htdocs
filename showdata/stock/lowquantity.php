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
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">缩量下跌</h1>

	<div class="wrapper">
		<a href="/showdata/showall.php" style="text-align:center;color:#ddd">首页</a>
	</div>

<?php

#echo date("l");

$day = 10;
$begin = 0;
$start = 1;    #起始天，当天为0，前一天为1，以此类推
for ($i=$begin;$i<$day;$i++){
	$date_array[$i] = date("Y-m-d",strtotime("-$i day"));
}
#print_r($date_array);

$conn=mysql_connect("localhost","root","root");
if(!$conn){
	echo "连接失败";
}

mysql_select_db("stock",$conn);
mysql_query("set names utf8");

$sql = "select * from `stock_data` where date = '$date_array[$start]' and source = 'stockstar' ";
#print($sql);

$res = mysql_query($sql,$conn);
#print($res);
while($row = mysql_fetch_row($res)){
	#print($row);
	$name = $row[1];
	$price[$name] = 100000.00;
	$quantity[$name] = 10000000000.00;
	$quantity_average[$name] = 0.0;
	#echo $price[$name];
	#echo $quantity[$name];
}

$ma5 = 0;
$flag = 0;
$period = 5;      #成交量在$period日内最低
$begin = 0;
while ($ma5 < $period){
	$sql="select * from `stock_data` where date = '$date_array[$begin]' and source = 'stockstar' ";
	$flag = 0;
	#echo $sql;
	$res = mysql_query($sql,$conn);
	while($row = mysql_fetch_row($res)){
		if ($row[10] != 'Sunday' && $row[10] != 'Saturday'){
		$name = $row[1];
		$quantity_average[$name] += (float)$row[3];
		if ((float)$row[3] < $quantity[$name]){
			$quantity[$name] = $row[3];
		}
		if ($price[$name] > (float)$row[7]){
			$price[$name] = $row[7];
		}
		#echo $price[$name];
		$flag = 1;
		}
	}
	if ($flag == 1){
		$ma5++;
		#echo $ma5;
	}
	#echo '----------------';
	#echo $ma5;
	$begin++;
	#echo 'begin--------------------------------';
	#echo $begin;
}

/*
echo 'quantity_average';
print_r($quantity_average);
echo 'price list';
print_r($price);
echo 'quantity list';
print_r($quantity);
*/

#echo $date_array[0];
$total = 0;
$today = date("Y-m-d");
$sql="select * from `stock_data` where date = '$date_array[$start]' and source = 'stockstar' ";
#echo $sql;

$res = mysql_query($sql,$conn);
while($row = mysql_fetch_row($res)){
	$name = $row[1]; 
	#echo $name;
	#echo $price[$name];
	#echo $quantity[$name];
	if ($quantity[$name] == $row[3] && $price[$name] == $row[7] && $quantity_average[$name]/$period != $quantity[$name]){
		$total++;
		$sql = "insert into analysis (name, date, reason) values ('$name', '$today', 'low_quantity_drop')";
		#echo $sql;
		mysql_query($sql,$conn);
		#echo $total;
	}
}

echo "<p style='text-align:center;color:#ddd;font-size:20px'>共 $total 支股票</p>";

$sql = "select * from `stock_data` where date = '$date_array[$start]' and source = 'stockstar' ";
$res = mysql_query($sql,$conn);
while($row=mysql_fetch_row($res)){
	$name = $row[1];
	if ($quantity[$name] == $row[3] && $price[$name] == $row[7] && $quantity_average[$name]/$period != $quantity[$name]){
		echo "<li class='show'><a href='/showdata/showall.php' style='color:#ddd'>$name</a></li>";
	}
}


?>
</body>
</html>