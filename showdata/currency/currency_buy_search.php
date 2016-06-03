<?php

$currency = $_POST['currency'];
# echo $currency;
SetCookie("currency",$currency); 

?>

<html>
<head>
	<style type='text/css'>
	.wrapper {
		text-align: center;
	}

	.list{
		text-align: center;
		margin: 5px;
		list-style: none;
		padding: 5px;
	}
	</style>
</head>

<body bgcolor="#32425c">
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">现钞买入价格</h1>
	<div class="wrapper">
		<ul class="list">
			<li>
				<a href="/showdata/showall.php" style="text-align:center;color:#ddd">首页</a>
			</li>
			<li>
				<a href="show_currency.php" style="text-align:center;color:#ddd">汇率列表页</a>
			</li>
		</ul>
	</div>
	<div class="wrapper">
		<a href="#" onclick="get_data()" style="text-align:center;color:#ddd">生成趋势图</a>
	<div>
		<canvas id="myChart" width="400" height="200"></canvas>
	</div>

	<script src="Chart.js"></script>
	<script>
	
	function get_data(){
		xmlhttp = new XMLHttpRequest();
		xmlhttp.open("GET","currencychosen.php",true);
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
				label: "买入价格",
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