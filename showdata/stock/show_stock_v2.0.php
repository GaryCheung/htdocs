<html>
<head>
	<style type='text/css'>
	.list{
		text-align: center;
		list-style: none;
		margin: 5px;
		margin-top: 10px;
	}


	.wrap{
		margin-left:47%;
		margin-top: 20px;
	}

	.wrap1{
		margin-left:48%;
		margin-top: 10px;
	}

	.wrap2{
		margin-left:48%;
		margin-top: 20px;
		color: #ddd;
	}
	</style>
</head>

<body bgcolor="#32425c">
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">Stock Infomation</h1>
	<ul class="list">
		<li class="list">
			<a href="/showdata/showall.php" style="text-align:center;color:#ddd">首页</a>
		<li>
	</ul>

	<div class="wrap">
		<a href="total.php" style="text-align:center;color:#ddd">运行全部程序</a>
	</div>
	<div class="wrap1">
		<a href="today_chosen_stock.php" style="text-align:center;color:#ddd">今日股票</a>
	</div>	

	<div class="wrap1">
		<a href="position.php" style="text-align:center;color:#ddd">股票价格位置</a>
	</div>
	<div class="wrap1">
		<a href="increase_statistic.php" style="text-align:center;color:#ddd">涨幅分布图</a>
	</div>
	<div class="wrap1">
		<a href="quantity_above_average.php" style="text-align:center;color:#ddd">成交量高于前10日平均成交量</a>
	</div>
	<div class="wrap1">
		<a href="drop_50.php" style="text-align:center;color:#ddd">从最高位跌幅50%以上</a>
	</div>

	<div class="wrap2">
	</div>
	<ul class="list">
		<li class="list">
			<a href="total_performance.php" style="text-align:center;color:#ff6100">今日股票表现</a>
		</li>
		<li class="list">
			<a href="above_all_increase.php" style="text-align:center;color:#ff6100">涨幅高于全部股票加权涨幅的股票</a>
		</li>
		<li class="list">
			<a href="above_5average.php" style="text-align:center;color:#ff6100">当前价格在5日均线上</a>
		</li>
		<li class="list">
			<a href="above_average_perform.php" style="text-align:center;color:#ff6100">当日价格>5日均线 & 涨幅高于加权涨幅</a>
		</li>
		<li class="list">
			<a href="score.php" style="text-align:center;color:#ff6100">得分最高100股</a>
		</li>
		<li class="list">
			<a href="strategy_one.php" style="text-align:center;color:#ff6100">收盘价>5日均线 & 涨幅大于全部股票加权涨幅 & 涨幅>0</a>
		</li>
		<li class="list">
			<a href="strategy_two.php" style="text-align:center;color:#ff6100">收盘价在历史低位 & 放量 & 涨幅>0</a>
		</li>
		<li class="list">
			<a href="strategy_three.php" style="text-align:center;color:#ff6100">历史低位 & 红三兵 ｜ 吞没</a>
		</li>
		<li class="list">
			<a href="strategy_amplitude_increase.php" style="text-align:center;color:#ff6100">成交量 > 0 & 涨幅 > 0 </a>
		</li>
	</ul>



</body>
</html>