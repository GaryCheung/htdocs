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

$day = 10;     #今天的振幅，是$day天内振幅最大的
$begin = 0;
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

$sql="select * from `stock_data` where date = '$date_array[$begin]' and source = 'xueqiu' ";
# echo $sql;
$res=mysql_query($sql,$conn);
while ($row=mysql_fetch_row($res)){
	#print($row[2]);
	$name = $row[1];
	$amplitude[$name] = $row[2];
	$amplitude_date[$name] = $row[4];
	$max_amplitude[$name] = $row[2];
	#print($stock_date);
	$max_amplitude_date[$name] = $row[4];
	$stock_name[$name] = $row[1];
	#echo $amplitude[$name],$amplitude['date'];
	#print($max_amplitude['date']);
	#print($max_amplitude[$name]);
	#print_r($max_amplitude);
}
#print(gettype($max_amplitude));
#foreach ($max_amplitude as $key=>$value){
#	echo $key.'=>'.$value;
#}
# echo count($max_amplitude);

for ($i=1;$i<$day;$i++){
	$sql="select * from `stock_data` where date = '$date_array[$i]' and source = 'xueqiu'";
	#echo $sql;
	#echo "###################################";
	$res=mysql_query($sql,$conn);
#print_r(mysql_fetch_array($res));
	while($row=mysql_fetch_row($res)){	
		$name = $row[1];
		$stock_date = $name.'date';
		#print($name);
		if ($max_amplitude[$name] <= $row[2]){
			#echo "get one!!!!!!";
			$max_amplitude[$name] = $row[2];
			$max_amplitude_date[$name] = $row[4];
			#print($date_array[$i]);
			#print($name);
		}
	}
}
#print_r($max_amplitude_date);

$length = count($max_amplitude);
#echo $length;
#print($max_amplitude['皖能电力(SZ:000543)']);
#print_r($max_amplitude);
$total = 0;


$today = date("Y-m-d");
$sql = "delete from analysis where date = '$today'";
mysql_query($sql,$conn);

foreach ($max_amplitude_date as $key=>$value){
	if ($max_amplitude_date[$key] == $amplitude_date[$key]){
		$sql = "insert into analysis (name, date, reason) values ('$stock_name[$key]', '$today', 'amplitude')";
		#echo $sql;
		mysql_query($sql,$conn);
		$total++;
		#print($stock_name[$key]);
		#echo "-------";
	}
}
echo "<p style='text-align:center;color:#ddd;font-size:20px'>共 $total 支股票</p>";

foreach ($max_amplitude_date as $key=>$value){
	if ($max_amplitude_date[$key] == $amplitude_date[$key]){
		#print($max_amplitude[$key]);
		#print($max_amplitude['date']);
		#print($amplitude[$key]);
		#print($amplitude['date']);
		#echo "-------------------------------------------";
		#print($amplitude[$i]);
		echo "<li class='show'><a href='stock_amplitude_list.php' style='color:#ddd'>$key</a></li>";
	}
}


?>

</body>
</html>