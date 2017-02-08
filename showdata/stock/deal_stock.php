<?php

$stock = $_POST['stock'];
$price = $_POST['price'];
$cost = $_POST['price_per_share'];
$stockid = $_POST['stockid'];
echo $stock;
echo $price;
echo $cost;

$today = date("Y-m-d");
echo $today;

$conn=mysql_connect("localhost","root","root");
if(!$conn){
	echo "连接失败";
}

mysql_select_db("stock",$conn);
mysql_query("set names utf8");

$sql = "update stock_holder set state = 'finished' where stockid = '$stockid' order by buy_date DESC limit 1";
$res = mysql_query($sql,$conn);

?>