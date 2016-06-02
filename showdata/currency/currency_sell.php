<html>
<head>
	<style type='text/css'>
	.wrapper {
		text-align: center;
		margin: 5px;
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
	</style>
</head>

<body bgcolor="#32425c">
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">汇率走势</h1>

	<div class="wrapper">
		<a href="/showdata/showall.php" style="text-align:center;color:#ddd">首页</a>
	</div>
	<div class="wrapper">
		<a href="show_currency.php" style="text-align:center;color:#ddd">汇率首页</a>
	</div>
	<div class="search">
		<form class="input" action="currency_sell_search.php" method="post">
			币种: <input type="text" name="currency"/>
			<input type="submit" value="Submit" />
		</form>
	</div>
	<div class="wrapper">
		<ul>
			<li style="float:left;display:inline;">
				<a href="currency_sell_chosen.php?currency=美元" style="text-align:center;color:#ddd">美元</a>
			</li>
			<li style="float:left;display:inline;">
				<a href="currency_sell_chosen.php?currency=欧元" style="text-align:center;color:#ddd">欧元</a>
			</li>
			<li style="float:left;display:inline;">
				<a href="currency_sell_chosen.php?currency=日元" style="text-align:center;color:#ddd">日元</a>
			</li>
			<li style="float:left;display:inline;">
				<a href="currency_sell_chosen.php?currency=林吉特" style="text-align:center;color:#ddd">林吉特</a>
			</li>
			<li style="float:left;display:inline;">
				<a href="currency_sell_chosen.php?currency=英镑" style="text-align:center;color:#ddd">英镑</a>
			</li>
		</ul>
		<div>
