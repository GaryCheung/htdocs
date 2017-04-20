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
 #######  参数设置区。 ratio涨幅比利，例如：0.05表示上涨5%  ########    
$ratio = 0.02; 
$out_ratio=$ratio*100;  

$factor_reason = ['chosen','amplitude','goldx','low_quantity_drop','max_quantity','gold5_10x','citou','tunmo','hongsanbing','blank','score100'];
$factor_test = $factor_reason[1];

$today = date("Y-m-d");
$day = 15; 


echo "股票上涨幅度为";
echo $out_ratio;
echo "%";
echo "<br>";


echo $day;
echo "<br>";
$date_begin = date("Y-m-d",strtotime("-$day day"));

echo $date_begin;

$conn=mysql_connect("localhost","root","root");
if(!$conn){
	echo "连接失败";
}

mysql_select_db("stock",$conn);
mysql_query("set names utf8");

#$sql = "select * from `analysis` where reason = '$factor_reason[0]' and date>= '$date_begin' ";
$sql = "select * from `analysis` where reason = '$factor_test' and date>= '$date_begin' ";
#$sql = "select * from `analysis` where reason = '$factor_reason[2]' and date>= '$date_begin' ";
#$sql = "select * from `analysis` where reason = '$factor_reason[3]' and date>= '$date_begin' ";
#$sql = "select * from `analysis` where reason = '$factor_reason[4]' and date>= '$date_begin' ";
echo $sql;
$res = mysql_query($sql,$conn);

$stock_selected = [];
$i = 0;
while($row = mysql_fetch_row($res)){
	#print_r($row);
	$stock_selected[$i++]=$row;
	#print_r($stock_selected);
	#echo "###################";
}

$length = sizeof($stock_selected);
#print_r($stock_selected[0]);
echo "<br>";
echo $length;

$j=0;
$sucess = 0;
$lose = 0;
$sum = 0;

while ($j++ < $length){
	$i=0;
	$stockid = $stock_selected[$j];
	#print_r($stockid);
	#echo "<br>";
	#echo $stockid[2];
	if (preg_match("/\(+\w*\W+\w*\)+/", $stockid[1], $stock_code))
		{
			#echo "yes";
			#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
		};
	#echo "stockid";
	#echo "<br>";
	#print_r($stockid);

	############# 判断是否能够真实成交  #############
	$sql = "select * from `stock_data` where stock_name like '%$stock_code[0]%' and date >= '$stockid[2]' limit 2";
	#echo $sql;
	#echo "<br>";
	#echo "$$$$$$$$$$";
	echo "<br>";
	$res = mysql_query($sql,$conn);
	while($row = mysql_fetch_row($res)){
		print_r($row);
		echo "<br>";
		$deal_date[$i] = $row[4];
		/*
		echo "@@@@@@@@@@@@";
		echo $deal_date[$i];
		echo "@@@@@@@";
		echo "<br>";
		echo "#########";
		echo $deal_date[1];
		echo "#########";
		echo "<br>";
		*/
		$close_price[$i] = $row[7];
		$low_price[$i++] = $row[9];
		#echo "<br>";
		#echo "$$$$$$$";
		#print_r($deal_date);
		#echo "$$$$$$";
		#echo $row[1];
		if (preg_match("/\(+\w*\W+\w*\)+/", $row[1], $stock_code))
			{
				#echo "yes";
				#print_r($stock_code);              ##########   $stock_code[0]存储股票代码  ###########   
			};
		#print_r($stock_code);
		#echo "$$$$$$$$$$$$";
	}
	/*
	print_r($deal_date);
	echo "<br>";
	print_r($close_price);
	echo "<br>";
	print_r($low_price);
	echo "<br>";

	echo $low_price[1];
	echo "<br>";
	echo $close_price[0];
	echo "<br>";
	*/
	if ($low_price[1] < $close_price[0] && $low_price[1] > 0){
		$buy_price = $close_price[0];
		if (sizeof($deal_date) == 1){
			$buy_day = $deal_date[0];
			$sell_day = $deal_date[0];
		}
		else{
			$buy_day = $deal_date[1];
			$sell_day = $deal_date[1];			
		}
		#echo $deal_date[1];

		print_r($deal_date);

		echo "buy_price";
		echo $buy_price;
		echo "<br>";
		
		echo "buy_day";
		echo $buy_day;
		echo "<br>";

		echo "sell_day========";
		echo $sell_day;
		echo "<br>";
		
		#############  判断是否能够卖出 #########################
		$sql = "select * from `stock_data` where stock_name like '%$stock_code[0]%' and date > '$sell_day' ";
		echo "<br>";
		echo $sql;
		$res = mysql_query($sql,$conn);
		$price_max = 0;
		#######  取最高股价 ###########
		while($row = mysql_fetch_row($res)){
			#echo "<br>";
			#print_r($row);
			if ($row[8]>$price_max){
				$price_max = $row[8];
				$max_day = $row[4];
			}
			if ($row[8]>=$buy_price*(1+$ratio) && $sell_day == $deal_date[1]){      ##########   高于买入价5%卖出,以第一次触碰到5%日期为成交日期    ############
				#echo "^^^^^^^";
				#echo $sell_day;
				#echo "<br>";
				#echo $stockid[2];
				$sell_day = $row[4];
				$sellday_highprice = $row[8];
			}
		}
		$one = strtotime($sell_day);//卖出时间 时间戳
		$tow = strtotime($buy_day);//买入时间 时间戳
		$cle = $one - $tow; //得出时间戳差值
		/*Rming()函数，即舍去法取整*/
	 	$d = floor($cle/3600/24);
	 	if ($d == 0){
			$increase = ($price_max/$buy_price-1)*100;
			echo "<li class='show'><p class='left' style='color:#ddd'>$stockid[1]以$out_ratio%涨幅规则,最大涨幅为$increase,</p><p class='right'>不能成交</p></li>";
			$lose++;
			echo "<br>";
		}
		else {
			echo "<li class='show'><p class='left' style='color:#ddd'>$stockid[1]以$out_ratio%涨幅成交，买入日期：$buy_day, 买入价格：$buy_price,卖出日期：$sell_day,卖出日最高价：$sellday_highprice</p></li>";	
			$sucess++;
		}	
	$sum =$sum+$d;
	#break;
	}
}

$sucess_rate = $sucess/($sucess+$lose)*100;
$sum = $sum / $sucess;
echo "<br>";
echo "<li class='show'><p class='result' font-size='20px'>$out_ratio%涨幅规则，成功率为：$sucess_rate%</p></li>";	
echo "<li class='show'><p class='result' font-size='20px'>平均成交时间是：$sum 天</p></li>";	
$performance = $sucess_rate / $sum * $out_ratio; 
echo "<li class='show'><p class='result' font-size='20px'>$out_ratio%策略得分(每万元每天收益)：$performance</p></li>";	

$sql = "delete from factor where date >= '$today' and factor = '$factor_test' and influence = '$performance'";
echo $sql;
$res = mysql_query($sql,$conn);

$sql = "insert into factor (date, factor, influence, increase, sucess_rate, period) values ('$today', '$factor_test', '$performance', '$out_ratio', '$sucess_rate', '$day')";
$res = mysql_query($sql,$conn);

?>

</body>
</html>

