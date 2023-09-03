
var RouteMarkers = new Array();
var RoutePath;

function ShowRoute(Line,Service)
{
	RemovePathStops();
	$.get( "https://map.gsupertrack.com/api.php?RouteandSections=1&Route=001&Service=N", { Route:Line,Service:Service } )
	  .done(function( data ) {
		var CoordinatesArray = JSON.parse(data);
		console.log(JSON.parse(data));
		AddPathToMap(CoordinatesArray);
		JPResultHideShow();
	  });
}

function ShowRoutebyJson(data)
{
	RemovePathStops();
	var CoordinatesArray = data.Vehicleinfo;
	AddPathToMap(CoordinatesArray);

}
function CreatePath(linecordinates)
{
	const path = L.polyline.antPath(pathArray(linecordinates), {
	  "delay": 400,
	  "dashArray": [10,20],
	  "weight": 5,
	  "color": "#0000FF",
	  "pulseColor": "#FFFFFF",
	  "paused": false,
	  "reverse": false,
	  "hardwareAccelerated": true
	});
	return path;
}

function pathArray(line_json){
var points =[];
var LatLng = [];
	for(var i=0; i< line_json.length; i++){
		
				LatLng = [];
				LatLng.push(line_json[i].lat,line_json[i].lng);
				points.push(LatLng);
				
		if(line_json[i].Velocity==0)
		{
			CreateStopPath(i,line_json[i].lat,line_json[i].lng,line_json[i].time,line_json[i].park);
		}
	}
return points;
}

function CreateStopPath(i,lat,lng,Time,Parking)
{
	StopMarkers[StopMarkerIndex] = L.marker([lat,lng]).bindPopup("<H4>"+Time+"</H4>"+"<H3>Parking: "+Parking+"</H3>").addTo(map);
	StopMarkers[StopMarkerIndex].setIcon(SetMapIconPark());
	StopMarkerIndex++;
}

function SetMapIconPark()
{
	return Park = L.icon({
		iconUrl: 'mapicon/Playback/Xplaybackpark.png',
		iconSize: [20, 20],
		iconAnchor: [10, 20],
		popupAnchor: [0, 0]
	});
}

function CreateBusStopIcon()
{
	var busstop = L.icon({
		iconUrl: 'mapicon/hubee/stops/busstop.png',
		iconSize:     [20, 20], // size of the icon
		iconAnchor:   [10,10], // point of the icon which will correspond to marker's location
		popupAnchor:  [0, -10] // point from which the popup should open relative to the iconAnchor
	});
	return busstop;
}

function AddPathToMap(linecordinates)
{
	RoutePath = CreatePath(linecordinates);
	map.addLayer(RoutePath);
	map.fitBounds(RoutePath.getBounds());
}

function CreateMarkers()
{
	var id = "";
	for(var i=0; i< pointscordinates.points.length; i++){
		id = pointscordinates.points[i].name;
		//alert(i);
		RouteMarkers[i] = L.marker([pointscordinates.points[i].lat,pointscordinates.points[i].lng],{icon: busstop}).bindPopup("<H1>"+id+"</H1>");
	}
}

function displayRouteStops()
{
	for(var i=0; i< pointscordinates.points.length; i++){
		RouteMarkers[i].addTo(map);
	}
}

function HideRouteStops()
{
	for(var i=0; i< pointscordinates.points.length; i++){
		map.removeLayer(RouteMarkers[i]);
	}
}

function RemovePathStops()
{	if(RoutePath != undefined)
	{
		map.removeLayer(RoutePath);
	}
}