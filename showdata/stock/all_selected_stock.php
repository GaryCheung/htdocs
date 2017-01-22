<?php



$conn=mysql_connect("localhost","root","root");
if(!$conn){
	echo "连接失败";
}

mysql_select_db("stock",$conn);
mysql_query("set names utf8");

$stock = [];
for ($i=0;$i<$day;$i++){
	#echo $i;
	$sql = "select * from `analysis` where date = '$date_array[$i]' ";
	$res = mysql_query($sql,$conn);
	#print($res);
	$flag = [];
	while($row = mysql_fetch_row($res)){
		#print_r($row);
		if (array_key_exists("$row[1]",$flag)){
		#echo $row[1];
		$flag[$row[1]] = $flag[$row[1]]+1;
		/*
		#echo $name;
		echo "**************";
		echo $flag[$row[1]];
		echo "<br>";
		*/
		}
		else{
			$flag[$row[1]] = 1;
		#echo $name;
		/*
		echo "#########";
		echo $flag[$row[1]];
		echo "<br>";
		*/
		} 
	}
	$total = 0;
	foreach ($flag as $key => $value) {
	#echo $flag[$key];
	#echo "##########";
	#echo "<br>";
	if ($flag[$key] == 3){
		#echo $key;
		#echo $total;
		$stock[$i][$total]=$key;
		$sql = "insert into analysis (name, date, reason) values ('$key', '$today', 'chosen')";
		$conn=mysql_connect("localhost","root","root");
		if(!$conn){
			echo "连接失败";
		}
		mysql_select_db("stock",$conn);
		mysql_query("set names utf8");
		#echo $stock[$i][$total];
		$total = $total+1;
	}
	}
}

##########################################   找到所有被选股票   #########################################

?>
