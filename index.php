<script type="application/javascript" src="awesomechart.js"></script>

<a href="#" onclick="get_data()">gold_price</a>

<div id="php100"></div>

<canvas id="canvas1" width="300" height="300">
        Your web-browser does not support the HTML 5 canvas element.
</canvas>

<script type="text/javascript" >

function get_data(){
	xmlhttp = new XMLHttpRequest();
	xmlhttp.open("GET","data.php",true);
	xmlhttp.onreadystatechange = byphp;
	xmlhttp.send(null); 
}

function byphp(){
	var byphp100 = xmlhttp.responseText;
	var data_deco = JSON.parse(byphp100);
	var data = data_deco.slice(0,4);
	var date = data_deco.slice(21,45);
	alert(date);
    if(!!document.createElement('canvas').getContext){ //check that the canvas
                                                   // element is supported
    var mychart = new AwesomeChart('canvas1');
    mychart.title = "Gold_Price";
    mychart.data = data;
    mychart.labels = date;
    mychart.draw();
	}
	//alert(data_deco.length);
	//alert(typeof(data_deco));
	document.getElementById('php100').innerHTML = byphp100;

}

</script>

