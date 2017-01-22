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
$today = date("Y-m-d");
echo $today;

$conn=mysql_connect("localhost","root","root");
if(!$conn){
	echo "连接失败";
}

mysql_select_db("stock",$conn);
mysql_query("set names utf8");

$sql = "select * from `analysis` where date = '$today' ";
$res = mysql_query($sql,$conn);
#print($res);
$flag = [];
while($row = mysql_fetch_row($res)){
	#print_r($row);
	#echo "$$$$$$$$$$$$$$$$";
	if (array_key_exists("$row[1]",$flag)){
		#echo $row[1];
		$flag[$row[1]] = $flag[$row[1]]+1;
		/*
		#echo $name;
		echo "**************";
		echo $flag[$row[1]];
		echo "<br>";
		*/
	}
	else{
		$flag[$row[1]] = 1;
		#echo $name;
		/*
		echo "#########";
		echo $flag[$row[1]];
		echo "<br>";
		*/
	} 
	/*
	echo $row[1];
	echo "#########";
	echo $flag[$row[1]];
	echo "<br>";
	*/
}

$total = 0;
foreach ($flag as $key => $value) {
	#echo $flag[$key];
	#echo "##########";
	#echo "<br>";
	if ($flag[$key] == 3){
		$total = $total+1;
	}
}
#echo $total;
echo "<p style='text-align:center;color:#ddd;font-size:20px'>共 $total 支股票</p>";


foreach ($flag as $key => $value) {
	if ($flag[$key] == 3){
		$sql = "insert into analysis (name, date, reason) values ('$key', '$today', 'chosen')";
		$conn=mysql_connect("localhost","root","root");
		if(!$conn){
		echo "连接失败";
		}
		mysql_select_db("stock",$conn);
		mysql_query("set names utf8");
		mysql_query($sql,$conn);
		#echo $key;
		echo "<li class='show'><a href='/showdata/showall.php' style='color:#ddd'>$key</a></li>";	
	}
}


?>
</body>
</html>