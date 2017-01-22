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
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">今日股票</h1>

	<div class="wrapper">
		<a href="/showdata/showall.php" style="text-align:center;color:#ddd">首页</a>
	</div>


<?php

$day = 150;     #计算最近$day天内的数据
$begin = 0;
#echo '-----------------';
#echo $begin;
for ($i=$begin;$i<$day;$i++){
	$date_array[$i] = date("Y-m-d",strtotime("-$i day"));
}
print_r($date_array);
$len = count($date_array);
echo $len;
$len--;
echo "<br>";

#############   规则1:盈利10%卖出，计算所需的天数   #######################

$sql = "select * from `analysis` where date >= '$date_array[$len]' and reason = 'chosen'";
#echo $sql;

$conn=mysql_connect("localhost","root","root");
if(!$conn){
	echo "连接失败";
}

mysql_select_db("stock",$conn);
mysql_query("set names utf8");
$res = mysql_query($sql,$conn);

$conn_stock=mysql_connect("localhost","root","root");
if(!$conn_stock){
	echo "连接失败";
}
mysql_select_db("stock",$conn_stock);
mysql_query("set names utf8");

$flag = 0;
$waste = [];
while($row=mysql_fetch_row($res)){
	echo "-----------------BEGIN------------------";
	$flagg = 0;
	echo "##################";
	#print_r($row);
	#echo "<br>";
	echo $flag++;
	echo "##################";
	echo $row[1];
	echo "##################";
	#echo "<br>";
	$date_select = $row[2];
	#echo "##################";
	#echo $date_select;
	#echo "<br>";
	#$sql_stock = "select * from `stock_data` where date >= '2016-11-02' and stock_name like '%ST明科(SH:600091)%'";
	$sql_stock = "select * from `stock_data` where date >= '$date_select' and stock_name like '%$row[1]%'";
	#echo $sql_stock;
	#echo "++++++++++++++++";
	$res_stock = mysql_query($sql_stock,$conn_stock);
	#echo $res_stock;
	$result = mysql_fetch_row($res_stock);
	#echo $result;
	#echo "---------------------";
	#print_r($result);
	#echo "<br>";
	$price_selected = $result[7];
	$waste_time = 0;
	while($result = mysql_fetch_row($res_stock)){
		echo "++++++++++++++++";
		echo $flagg++;
		echo "++++++++++++++++";
		if ($result[9] >= $price_selected*1.05){
			$waste_time++;
			echo "%%%%%%%%%";
			echo $result[9];
			echo "^^^^^^";
			echo $price_selected*1.05;
			echo "%%%%%%%%%%%";
			break;
		}else{
			$waste_time++;
		}
	}
	echo "$$$$$$$$$$$$$";
	echo $waste_time;
	echo "$$$$$$$$$$$$$";
	$waste[$flag]=$waste_time;
	echo "------------   END   --------------";
	echo "<br>";
}

print_r($waste);
$total_stock = count($waste);
$average_day = 0;
foreach ($waste as $key => $value) {
	$average_day = $average_day + $value;
}
$average_day = $average_day / $total_stock;
echo "<br>";
echo "!!!!!!!!!!!!!!!!!";
echo $average_day;
echo "!!!!!!!!!!!!!!!!!";

$sql_del = "delete from `sucess` where date >= '$date_array[0]' and strategy = '5%_to_sell'";
$conn=mysql_connect("localhost","root","root");
if(!$conn){
	echo "连接失败";
}
mysql_select_db("stock",$conn);
mysql_query("set names utf8");
mysql_query($sql_del,$conn);

$sql_sucess = "insert into `sucess` (date, waste, strategy) values ('$date_array[0]', '$average_day', '5%_to_sell')";
#echo $date_array[0];
#echo $average_day;
echo $sql_sucess;
$conn=mysql_connect("localhost","root","root");
if(!$conn){
	echo "连接失败";
}
mysql_select_db("stock",$conn);
mysql_query("set names utf8");
mysql_query($sql_sucess,$conn);
echo "------------END------------";

?>
</body>
</html>


