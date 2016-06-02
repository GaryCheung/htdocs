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
	</style>
</head>

<body bgcolor="#32425c">
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">已成交房源信息</h1>

	<div class="wrapper">
		<a href="/showdata/showall.php" style="text-align:center;color:#ddd">首页</a>
	</div>

	<div class="search">
		<form class="input" action="house_boughtdata.php" method="post">
			小区名: <input type="text" name="house" />
			<select name="layout">
				<option value=""></option>
				<option value="1室">1室</option>
				<option value="2室">2室</option>
				<option value="3室">3室</option>
			</select>
			<input type="submit" value="Submit" />
		</form>
	</div>

</body>
</html>