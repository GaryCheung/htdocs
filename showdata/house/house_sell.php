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

	.sub{
		color:#ddd;
	}

	.list{
		text-align: center;
	}

	.content{
		list-style: none;
		margin-left: 38%;
		text-align: center;
	}

	.range{
		margin-left: -60px;
		margin-top: 15px;
		margin-bottom: 15px;
	}
	</style>
</head>

<body bgcolor="#32425c">
	<h1 style="font-family:Open Sans;text-align:center;color:#fff;font-size:60px;margin:25px">在售房源</h1>

	<div class="wrapper">
		<a href="/showdata/showall.php" style="text-align:center;color:#ddd">首页</a>
	</div>
	<div class="search">
		<form class="sub" action="house_selldata.php" method="post">
			<ul class="list">
				<li class="content">
					小区名：<input type="text" name="house">
				</li>
				<li class="content">
					面积：  <input type="text" name="bottom" style="width:50px">  -  <input type="text" name="top" style="width:50px">
				</li>
				<li class="content">
					<input type="submit" value="Submit" >
				</li>
			</ul>
		</form>
	</div>
</body>
</html>

