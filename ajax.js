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
	var len = data_deco.length;
	// alert(len);
	var days = 10;
	var data = data_deco.slice(0,days-1);
	var date = data_deco.slice(len/2,len/2+days-1);
	//alert(date);
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
	//document.getElementById('php100').innerHTML = byphp100;

}

</script>
