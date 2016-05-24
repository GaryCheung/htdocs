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
				<a href="show.php" style="text-align:center;color:#ddd">Gold_Price</a>
			</li>
			<li class="list">
				<a href="show_house.php" style="text-align:center;color:#ddd">House</a>
			</li>
			<li class="list">
				<a href="show_vegetable.php" style="text-align:center;color:#ddd">Vegetable</a>
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