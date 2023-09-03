<!doctype html>
<html>
<head>
	<title>Geofence Settings</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!--<link href="style.css" type="text/css" rel="stylesheet" />-->
<style>
html, body {
	margin: 0;
	padding: 0;
	width: 100%;
	height: 100%; 
}

.content {
	min-height: 100%;
	position: relative;
	overflow: auto;
	z-index: 0; 
}

.content2 {
	min-height: 50px;
	position: absolute;
	overflow: auto;
	z-index: 0; 
}

.background {
	position: absolute;
	z-index: -1;
	top: 0;
	bottom: 0;
	margin: 0;
	padding: 0;
}

.top_block {
	width: 100%;
	display: block; 
}

.bottom_block {
	position: absolute;
	width: 100%;
	display: block;
	bottom: 0; 
}

.left_block {
	display: block;
	float: left; 
}

.right_block {
	display: block;
	float: right; 
}

.center_block {
	display: block;
	width: auto; 
}

.top_block {
	width: 100%;
	height: 70px;
	background-color:#686868;
}

.background.left_block {
	height: auto !important;
	padding-bottom: 0;
	left: 0;
	width: 350px;
	background-color: #fafaff;
	margin-top: 70px; 
}

.left_block {
	height: auto;
	width: 350px;
	padding-bottom: 0px;
}

.background.map_block {
	height: auto !important;
	padding-bottom: 0;
	left: 0;
	right: 0;
	background-color: #c6d3f2;
	margin-top: 70px;
	margin-left: 350px; 
}

.map_block {
	width: auto;
	height: auto;
	padding-bottom: 0px;
}

</style>
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCqKqoUdBycZ8rbh2lK3FME1f0x4URHO_Q&sensor=false"></script>
<!--<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>-->
    <script type="text/javascript">

      var shape;
	  var map;
	  var map_click_listner;
	  var geofence_click_listner;
      function initialize() {
        var mapDiv = document.getElementById('map-canvas');
        map = new google.maps.Map(mapDiv, {
          center: new google.maps.LatLng(7.293499,80.640997),
          zoom: 8,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        });
		loadgeolist();
		shape_int();
        //map_click_listner = google.maps.event.addListener(map, 'click', addPoint);
		//google.maps.event.addListener(shape, 'click', geofence_click);
      }
	  
	  function shape_int(){
	    shape = new google.maps.Polygon({
          strokeColor: '#ff0000',
          strokeOpacity: 0.8,
          strokeWeight: 2,
          fillColor: '#ff0000',
          fillOpacity: 0.35
        });
      
        shape.setMap(map);
	  }
	  
function add_listner(){
clear_fence();
map_click_listner = google.maps.event.addListener(map, 'click', addPoint);
geofence_click_listner = google.maps.event.addListener(shape, 'click', geofence_click);
}
      
function addPoint(e) {
	var vertices = shape.getPath();
	vertices.push(e.latLng);
}

function show_add_point(elatlng) {
	map.setCenter(elatlng);
	var vertices = shape.getPath();
	vertices.push(elatlng);
}

function geofence_click(e){
//inseart_fence();

var gname=prompt("Please enter Geofence name");
var remarks ="";

	if (gname!=null)
	  {
	  remarks=prompt("Remarks");
	  inseart_fence(gname,remarks);
	  }
}

function clear_fence() {
google.maps.event.removeListener(map_click_listner);
	shape.setMap(null);
	shape = [];
	shape_int();
}
	  
function show_gfence(coordinates){
	google.maps.event.removeListener(map_click_listner);
	google.maps.event.removeListener(geofence_click_listner);
	clear_fence();
	var latlng;
	coordninates_array = document.getElementById(coordinates).innerHTML.split(" ");

	for(var i=0; i<coordninates_array.length; i++){
	latlngarray = coordninates_array[i].split(",");
	latlng = new google.maps.LatLng(latlngarray[1],latlngarray[0]);
	show_add_point(latlng);
	}
	map.setZoom(16);
}

function loadgeolist(){
document.getElementById('geo_list').innerHTML ="";
		var xmlhttp;
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			    {
				document.getElementById('geo_list').innerHTML = xmlhttp.responseText;
			    }
		  }
	xmlhttp.open("GET","geo_table.php");
	xmlhttp.send();
}

function del_fence(rowid){
var del_id=document.getElementById('id'+rowid).innerHTML;
var del_name=document.getElementById('name'+rowid).innerHTML;

if (confirm('Are you sure you want to Delete '+del_name+'?')) {

		var xmlhttp;
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			    {
				alert(xmlhttp.responseText);
				loadgeolist();
			    }
		  }
	xmlhttp.open("GET","geo_delete.php?del_id="+del_id+"&del_name="+del_name);
	xmlhttp.send();
} else {
    // Do nothing!
}
clear_fence();
}

function inseart_fence(gname,remark){

var vertices = shape.getPath();
var coordinate_string="";
for(var i=0; i< vertices.length; i++){
coordinate_string = prepare_coordinates(vertices.getAt(i).toUrlValue(14))+",0 "+coordinate_string;
}
coordinate_string = prepare_coordinates(vertices.getAt(0).toUrlValue(14))+",0 "+coordinate_string;

function prepare_coordinates(coordinat){
precode = coordinat.split(",");
var return_code = precode[1]+","+precode[0];
return return_code;
}
//alert(coordinate_string);

		var xmlhttp;
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			    {
				alert(xmlhttp.responseText);
				loadgeolist();
			    }
		  }
	xmlhttp.open("GET","geo_insert.php?coordinates="+coordinate_string+"&gname="+gname+"&remarks="+remark);
	xmlhttp.send();

}

google.maps.event.addDomListener(window, 'load', initialize);
</script>
</head>
<body>
	<div class="content">
		<div class="top_block" >

			<div class="content2" style='right:10px; top:20px'>
				<button onclick="clear_fence()">Remove Fence</button>
				<button onclick="add_listner()">Add Fence</button>
			</div>
		</div>
		<div class="background left_block" id="geo_list" style='overflow:auto; overflow-x: hidden;'>
		</div>
		<div class="left_block">
			<div class="content">
			</div>
		</div>
		<div class="background map_block" id="map-canvas">
		</div>
	</div>
</body>
</html>
