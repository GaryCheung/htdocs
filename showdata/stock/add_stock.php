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

$sql = "insert into stock_holder (stock_name, buy_date, total_cost, price_per_share, stockid, state) values ('$stock', '$today', '$price', '$cost', '$stockid', 'buy')";
$res = mysql_query($sql,$conn);

?>