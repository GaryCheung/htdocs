<html>
<head>
	<style type='text/css'>
	.wrapper {
		text-align: center;
	}

	li{
		width: 40%;
		margin: 1px;
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
		margin-left: 30%;
		margin-top: 10px;
		color:#ddd;
	}

	.result{
		float: left;
		font-size: 26px;
		color:#FF6600;
	}

	.left{
		float: left;
	}

	.right{
		float: left;
		color: #dd0;
	}

	</style>
</head>

<body bgcolor="#32425c">
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">持仓情况</h1>

	<div class="wrapper">
		<a href="/showdata/showall.php" style="text-align:center;color:#ddd">首页</a>
	</div>

<?php
 #######  涨幅比利，例如：0.05表示上涨5%  ########    
$ratio = 0.02;   

$today = date("Y-m-d");
echo $today;

$conn=mysql_connect("localhost","root","root");
if(!$conn){
	echo "连接失败";
}

mysql_select_db("stock",$conn);
mysql_query("set names utf8");

$sql = "select * from `stock_holder` where state = 'buy' ";
$res = mysql_query($sql,$conn);
#print($res);
$stock_selected = [];
$i = 0;
while($row = mysql_fetch_row($res)){
	#print_r($row);
	$stock_selected[$i++]=$row;
	#print_r($stock_selected);
	#echo "###################";
}
#print_r($stock_selected);
#echo "#############";

$length = sizeof($stock_selected);
$sucess = 0;
$lose = 0;
$sum = 0;
$out_ratio=$ratio*100;

while ($length-- > 0){
	$stockid = $stock_selected[$length];
	#print_r($stockid);
	$sql = "select * from `stock_data` where stock_name like '%$stockid[5]%' and date > '$stockid[3]' ";
	$res = mysql_query($sql,$conn);
	$price_max = 0;
	$deal_day = $stockid[3];
	#echo $deal_day;
	#######  取最高股价 ###########
	while($row = mysql_fetch_row($res)){
		if ($row[8]>$price_max){
			$price_max = $row[8];
			$max_day = $row[4];
		}
		if ($row[8]>=$stockid[4]*(1+$ratio) && $deal_day == $stockid[3]){      ##########   高于买入价5%卖出,以第一次触碰到5%日期为成交日期    ############
			$deal_day = $row[4];
		}
	}
	$one = strtotime($deal_day);//成交时间 时间戳
	$tow = strtotime($stockid[3]);//买入时间 时间戳
	$cle = $one - $tow; //得出时间戳差值
/* 这个只是提示
echo floor($cle/60); //得出一共多少分钟
echo floor($cle/3600); //得出一共多少小时
echo floor($cle/3600/24); //得出一共多少天
*/
/*Rming()函数，即舍去法取整*/
	$d = floor($cle/3600/24);
	#$ratio = $ratio * 100;
	#echo $d;
	#echo "$$$$$$$$$";
	if ($d == 0){
		$increase = ($price_max/$stockid[4]-1)*100;
		echo "<li class='show'><p class='left' style='color:#ddd'>$stockid[1]以$out_ratio%涨幅规则,最大涨幅为$increase,</p><p class='right'>不能成交</p></li>";
		$lose++;
		echo "<br>";
	}
	else {
		echo "<li class='show'><p class='left' style='color:#ddd'>$stockid[1]以$out_ratio%涨幅成交，历时 $d 天,卖出日期：$deal_day</p></li>";	
		$sucess++;
	}	
	$sum =$sum+$d;
}

$sucess_rate = $sucess/($sucess+$lose)*100;
$sum = $sum / $sucess;
echo "<br>";
echo "<li class='show'><p class='result' font-size='20px'>$out_ratio%涨幅规则，成功率为：$sucess_rate%</p></li>";	
echo "<li class='show'><p class='result' font-size='20px'>平均成交时间是：$sum 天</p></li>";	
$performance = $sucess_rate / $sum * $out_ratio; 
echo "<li class='show'><p class='result' font-size='20px'>$out_ratio%策略得分(每万元每天收益)：$performance</p></li>";	
?>

</body>
</html>