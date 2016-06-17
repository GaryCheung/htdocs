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
		margin-left:40px;
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

$conn=mysql_connect("localhost","root","root");
if(!$conn){
	echo "连接失败";
}

mysql_select_db("vegetable",$conn);
mysql_query("set names utf8");


$sql1="select * from `vegetable` where date = '$yesterday'";
#echo $sql1;
$res_yesterday=mysql_query($sql1,$conn);
#print_r(mysql_fetch_array($res));

while($row=mysql_fetch_row($res_yesterday)){
	$name_yesterday = $row[2];
	$price_yesterday[$name_yesterday] = $row[5]; 
	#echo $name_yesterday[$j],$price_yesterday[$j];
}

$sql2="select * from `vegetable` where date = '$prensent_date'";
#echo $sql2;
$res_today=mysql_query($sql2,$conn);
#print_r(mysql_fetch_array($res));

while($row=mysql_fetch_row($res_today)){
	$name_today = $row[2];
	$name_today_total[$j++] = $row[2];
	$price_today[$name_today] = $row[5]; 
	#echo $name_today[$j],$price_today[$j];
}

$length = count($name_today_total);
#echo $length;


for ($i=0; $i<$length; $i++){
	$name = $name_today_total[$i];
	if ($price_today[$name] < $price_yesterday[$name]){	
		echo "<a href='vegetable.php?vege=$name_today_total[$i]' style='text-align:center;color:#ddd' class='show'>$name,$price_today[$name]-----$price_yesterday[$name]</a>";
	}
}	

?>

</body>
</html>