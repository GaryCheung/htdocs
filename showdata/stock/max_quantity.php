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
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">备选股票</h1>

	<div class="wrapper">
		<a href="/showdata/showall.php" style="text-align:center;color:#ddd">首页</a>
	</div>

<?php

#echo date("l");

$day = 10;     #今天的成交量，是$day天内振幅最大的
$begin = 0;    #从$begin天之前开始计算，begin＝1，表示昨天；begin＝0表示今天
echo '------------';
echo $begin;
for ($i=$begin;$i<$day;$i++){
	$date_array[$i] = date("Y-m-d",strtotime("-$i day"));
}
#print_r($date_array);


$conn=mysql_connect("localhost","root","19860112");
if(!$conn){
	echo "连接失败";
}

mysql_select_db("stock",$conn);
mysql_query("set names utf8");

$start = 0;
$sql="select * from `stock_data` where date = '$date_array[$begin]' and source = 'stockstar' ";
#echo $sql;
$res=mysql_query($sql,$conn);
while ($row=mysql_fetch_row($res)){
	#print($row[2]);
	$name = $row[1];
	$quantity[$name] = $row[3];
	$quantity_date[$name] = $row[4];
	#echo $quantity_date[$name];
	#echo $row[4];
	$max_quantity[$name] = $row[3];
	#print($stock_date);
	$max_quantity_date[$name] = $row[4];
	$stock_name[$name] = $row[1];
}
#print_r($quantity_date);

for ($i=1;$i<$day;$i++){
	$sql="select * from `stock_data` where date = '$date_array[$i]' and source = 'stockstar'";
	#echo $sql;
	#echo "###################################";
	$res=mysql_query($sql,$conn);
#print_r(mysql_fetch_array($res));
	while($row=mysql_fetch_row($res)){	
		$name = $row[1];
		$stock_date = $name.'date';
		#print($name);
		if ($max_quantity[$name] <= $row[3]){
			#echo "get one!!!!!!";
			$max_quantity[$name] = $row[3];
			$max_quantity_date[$name] = $row[4];
			#print($date_array[$i]);
			#print($name);
		}
	}
}
#print_r($max_quantity_date);

$length = count($max_quantity);
$total = 0;


$today = date("Y-m-d");
$sql = "delete from analysis where date = '$today' and reason = 'max_quantity'";
mysql_query($sql,$conn);

foreach ($max_quantity_date as $key=>$value){
	/*
	echo $key;
	echo $max_quantity[$key];
	echo '-------------';
	echo $quantity[$key];
	echo '-------------';
	*/
	#echo $quantity_date[$key];
	if ($max_quantity_date[$key] == $quantity_date[$key]){
		$sql = "insert into analysis (name, date, reason) values ('$stock_name[$key]', '$today', 'max_quantity')";
		#echo $sql;
		mysql_query($sql,$conn);
		$total++;
	}
}
echo "<p style='text-align:center;color:#ddd;font-size:20px'>共 $total 支股票</p>";

foreach ($max_quantity_date as $key=>$value){
	if ($max_quantity_date[$key] == $quantity_date[$key]){
		echo "<li class='show'><a href='show_stock.php' style='color:#ddd'>$key</a></li>";
	}
}


?>

</body>
</html>