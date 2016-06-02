<?php

$house = $_POST["house"];
$bottom = $_POST["bottom"];
$top = $_POST["top"];
#echo $house;

SetCookie("sell[house]",$house); 
SetCookie("sell[areabottom]",$bottom); 
SetCookie("sell[areatop]",$top); 
#print_r($_COOKIE);

?>

<html>
<head>
	<style type='text/css'>
	.wrapper {
		text-align: center;
		margin: 10px;
	}

	.link{
		margin: 15px;
	}
	</style>
</head>

<body bgcolor="#32425c">
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">在售房源价格走势</h1>
	<div class="wrapper">
		<a href="/showdata/showall.php" style="text-align:center;color:#ddd">首页</a>
	</div>
	<div class="wrapper">
		<a href="show_house.php" style="text-align:center;color:#ddd">房产信息页</a>
	</div>
	<div class="wrapper">
		<a href="house_sell.php" style="text-align:center;color:#ddd">在售房源信息页</a>
	</div>
	<div class="wrapper">
		<a class="link" href="#" onclick="get_data()" style="text-align:center;color:#ddd">挂牌单价</a>
		<a href="#" onclick="get_data_total()" style="text-align:center;color:#ddd">挂牌总价</a>
	<div>
		<canvas id="myChart" width="400" height="200"></canvas>
	</div>

	<script src="Chart.js"></script>
	<script>
	
	function get_data(){
		xmlhttp = new XMLHttpRequest();
		xmlhttp.open("GET","house_sell_data.php",true);
		xmlhttp.onreadystatechange = draw;
		xmlhttp.send(null); 
	}

	function get_data_total(){
		xmlhttp = new XMLHttpRequest();
		xmlhttp.open("GET","house_sell_data_total.php",true);
		xmlhttp.onreadystatechange = draw;
		xmlhttp.send(null); 
	}

	function draw(){
		var result = xmlhttp.responseText;
		// window.alert(result);
		var data_deco = JSON.parse(result);
		// window.alert(data_deco);
		var len = data_deco.length;
		var days = len;
		var price = data_deco.slice(0,days-1);
		var date = data_deco.slice(len/2,len/2+days-1);
		var data = {
			labels: date,
			datasets: [
			{
				label: "挂牌价格",
				backgroundColor: "rgba(75,192,192,0.4)",
				borderColor: "rgba(75,192,192,1)",
				pointBorderColor: "rgba(75,192,192,1)",
				pointBackgroundColor: "#fff",
				pointHoverBackgroundColor: "rgba(75,192,192,1)",
				pointHoverBorderColor: "rgba(220,220,220,1)",
				data: price,
			}
			]
		};
		var ctx = document.getElementById("myChart");
		var myLineChart = new Chart(ctx, {
			type: 'line',
			data: data
		});		
	}
	</script>

</body>
</html>