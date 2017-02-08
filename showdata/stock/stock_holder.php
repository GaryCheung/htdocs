<html>
<head>
	<style type='text/css'>
	.wrapper {
		text-align: center;
	}

	li{
		width: 40%;
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
		margin-left: 30%;
		margin-top: 10px;
		color:#ddd;
	}

	p{
		font-size: 20px;
		color: #dd0;
	}
	</style>
</head>

<body bgcolor="#32425c">
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">持仓变动</h1>

	<div class="wrapper">
		<a href="/showdata/showall.php" style="text-align:center;color:#ddd">首页</a>
	</div>

	<div class="search">
		<p>买入股票</p>
		<form class="input" action="add_stock.php" method="post">
			股票名: <input type="text" name="stock" />
			买入总价: <input type="text" name="price" />
			每股价格: <input type="text" name="price_per_share" />
			股票代码: <input type="text" name="stockid" />
			<input type="submit" value="添加交易" />
		</form>
	</div>

	<div class="search">
		<p>卖出股票</p>
		<form class="input" action="deal_stock.php" method="post">
			股票名: <input type="text" name="stock" />
			卖出总价: <input type="text" name="price" />
			每股价格: <input type="text" name="price_per_share" />
			股票代码: <input type="text" name="stockid" />
			<input type="submit" value="完成交易" />
		</form>
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

$sql = "select * from `stock_holder` where state = 'buy'";
$res = mysql_query($sql,$conn);
#print($res);
$flag = 0;
while($row = mysql_fetch_row($res)){
	#print_r($row);
	echo "<li class='show'><a href='/showdata/show_stock.php' style='color:#ddd'>$row[1]－－－买入总价：$row[2]－－－每股单价：$row[4]</a></li>";	
	$flag++;
}

echo "<p style='text-align:center;color:#ddd;font-size:20px'>共 $flag 支股票</p>";






?>


</body>
</html>