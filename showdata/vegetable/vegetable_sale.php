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
		margin:15%;
	}
	</style>
</head>

<body bgcolor="#32425c">
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">降价蔬菜</h1>

	<div class="wrapper">
		<a href="/showdata/showall.php" style="text-align:center;color:#ddd">首页</a>
	</div>
	<div class="search">
		<form class="input" action="vegetabledata.php" method="post">
			蔬菜名: <input type="text" name="vegetable" />
			<input type="submit" value="Submit" />
		</form>
	</div>


<?php

$prensent_date = date("Y-m-d");
#print($prensent_date);
$yesterday = date("Y-m-d",strtotime("-1 day"));
#print($yesterday);

$conn=mysql_connect("localhost","root","19860112");
if(!$conn){
	echo "连接失败";
}

mysql_select_db("vegetable",$conn);
mysql_query("set names utf8");


$sql1="select * from `vegetable` where date = '$yesterday'";
#echo $sql1;
$res_yesterday=mysql_query($sql1,$conn);
#print_r(mysql_fetch_array($res));

$j = 0;
while($row=mysql_fetch_row($res_yesterday)){
	$name_yesterday[$j] = $row[2];
	$price_yesterday[$j] = $row[5]; 
	#echo $name_yesterday[$j],$price_yesterday[$j];
	$j++;
}

$sql2="select * from `vegetable` where date = '$prensent_date'";
#echo $sql2;
$res_today=mysql_query($sql2,$conn);
#print_r(mysql_fetch_array($res));

$j = 0;
while($row=mysql_fetch_row($res_today)){
	$name_today[$j] = $row[2];
	$price_today[$j] = $row[5]; 
	#echo $name_today[$j],$price_today[$j];
	$j++;
}

$length = count($name_today);
#echo $length;


for ($i=0; $i<$length; $i++){
	if ($price_today[$i] < $price_yesterday[$i]){	
		echo "<a href='vegetable.php?vege=$name_today[$i]' style='text-align:center;color:#ddd' class='show'>$name_today[$i]</a>";
	}
}	

?>

</body>
</html>