var xmlhttp

function get_data(){
	xmlhttp = new XMLHttpRequest();
	xmlhttp.open("GET","data.php",true);
	xmlhttp.onreadystatechange = byphp;
	xmlhttp.send(null); 
}

function byphp(){
	var byphp100 = xmlhttp.responseText;
	alert(byphp100);
	document.getElementById('php100').innerHTML = byphp100;
}

