<html>
<head>
	<style type='text/css'>
	.wrapper {
		text-align: center;
	}

	.list{
		margin: 10px;
		list-style: none;
	}
	</style>
</head>

<body bgcolor="#32425c">
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">数据展示</h1>
	
	<div class="wrapper">
		<ul>
			<li class="list">
				<a href="./gold/show.php" style="text-align:center;color:#ddd">金价</a>
			</li>
			<li class="list">
				<a href="./house/show_house.php" style="text-align:center;color:#ddd">房产信息</a>
			</li>
			<li class="list">
				<a href="./vegetable/show_vegetable.php" style="text-align:center;color:#ddd">菜价</a>
			</li>
			<li class="list">
				<a href="./stock/show_stock.php" style="text-align:center;color:#ddd">股票</a>
			</li>
			<li class="list">
				<a href="./currency/show_currency.php" style="text-align:center;color:#ddd">汇率</a>
			</li>
		</ul>
	<div>
		<canvas id="myChart" width="400" height="200"></canvas>
	</div>

	<script src="Chart.js"></script>
	<script>
	
	function get_data(){
		xmlhttp = new XMLHttpRequest();
		xmlhttp.open("GET","data.php",true);
		xmlhttp.onreadystatechange = draw;
		xmlhttp.send(null); 
	}


	function draw(){
		var result = xmlhttp.responseText;
		var data_deco = JSON.parse(result);
		var len = data_deco.length;
		var days = len;
		var price = data_deco.slice(0,days-1);
		var date = data_deco.slice(len/2,len/2+days-1);
		var data = {
			labels: date,
			datasets: [
			{
				label: "Gold Price Trend",
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