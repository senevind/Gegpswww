var editableLayers;
var geomatryArray;



$(document).ready(function(){

});
function geoDrawInitialize()
{			
    editableLayers = new L.FeatureGroup();
    map.addLayer(editableLayers);
    
    var MyCustomMarker = L.Icon.extend({
        options: {
            shadowUrl: null,
            iconAnchor: new L.Point(12, 12),
            iconSize: new L.Point(24, 24),
            iconUrl: 'link/to/image.png'
        }
    });
    
    var options = {
        position: 'topleft',
        draw: {
			polyline:false,
			/*
            polyline: {
                shapeOptions: {
                    color: '#f357a1',
                    weight: 10
                }
            },
			*/
            polygon: {
                allowIntersection: true, // Restricts shapes to simple polygons
                drawError: {
                    color: '#e1e100', // Color the shape will turn when intersects
                    message: '<strong>Oh snap!<strong> you can\'t draw that!' // Message that will show when intersect
                },
                shapeOptions: {
                    color: '#bada55'
                }
            },
            circle: false, // Turns off this drawing tool
            rectangle: {
                shapeOptions: {
                    clickable: false
                }
            },
            marker: {
                //icon: new MyCustomMarker()
            }
        },
        edit: {
            featureGroup: editableLayers, //REQUIRED!!
            remove: true
        }
    };
    
    var drawControl = new L.Control.Draw(options);
    map.addControl(drawControl);
    
    map.on(L.Draw.Event.CREATED, function (e) {
        var type = e.layerType,
            layer = e.layer;
    
        if (type === 'marker') {
            //layer.bindPopup('A popup!');
        }
    
        editableLayers.addLayer(layer);
    });
	
	map.on('draw:created', function (e) {
	  var type = e.layerType;
	  var layer = e.layer;

	  var shape = layer.toGeoJSON();
	  saveDB(shape);
	  var shape_for_db = JSON.stringify(shape);
	  console.log(shape);
	});
}

function saveDB(geoArray)
{
	RefreshSaveInputs();
	
	if(geoArray.geometry.type == "LineString")
	{
		addLine(geoArray);
	}
	if(geoArray.geometry.type == "Polygon")
	{
		geomatryArray = geoArray;
		$('#PolygonSaveModel').modal('show');
	}
	if(geoArray.geometry.type == "Point")
	{
		geomatryArray = geoArray;
		$('#PointSaveModel').modal('show');
		
		//
	}
}

function RefreshSaveInputs()
{
	document.getElementById('PointName').value = "";
	document.getElementById('PolygonName').value = "";
	document.getElementById('pointValidationHelp').innerHTML = "";
	document.getElementById('polygonValidationHelp').innerHTML = "";
}

function btnClickSavePolygon()
{
	var polygonName = document.getElementById('PolygonName').value;
	if(!validateAddress(polygonName))
	{
		document.getElementById('polygonValidationHelp').innerHTML = "Please remove special charactors!";
		return false;
	}
	if(!ValidateSpaces(polygonName)){
		document.getElementById('polygonValidationHelp').innerHTML = "Please add geofence name!";
		return false;
	}
	addPolygon(geomatryArray,polygonName);
}

function btnClickSavePoint()
{
	var pointName = document.getElementById('PointName').value;
	if(!validateAddress(pointName))
	{
		document.getElementById('pointValidationHelp').innerHTML = "Please remove special charactors!";
		return false;
	}
	if(!ValidateSpaces(pointName)){
		document.getElementById('pointValidationHelp').innerHTML = "Please add geofence name!";
		return false;
	}
	addPoints(geomatryArray,pointName);
	
}

function addLine(geoArray)
{
	console.log(geoArray.geometry.coordinates);
}

function addPolygon(geoArray,polygonName)
{
	var latlngs = geoArray.geometry.coordinates[0];
	var coordinatesStr = "";
	for (var i = 0; i < latlngs.length; i++) {
		coordinatesStr = latlngs[i][0] + "," + latlngs[i][1] + "," + "0 " + coordinatesStr;
	}
	
	$.get( "./geohandle.php", { coordinates: coordinatesStr, gname: polygonName,reson: "insert",user:userName} )
	  .done(function( data ) {
		  toastr.warning(data);
		  editableLayers.clearLayers();
	  });
	  $('#PolygonSaveModel').modal('hide');
	//var url= "./geohandle.php?coordinates="+coordinatesStr+"&gname="+gname+"&user="+userName+"&reson=insert";
	//console.log(coordinatesStr);
}

function addPoints(geoArray,pointName)
{
	var latlngs = geoArray.geometry.coordinates;
	
	$.get( "./geohandle.php", { pointlat: latlngs[1], pointname: pointName, pointlong: latlngs[0],reson: "insertpoint",user:userName} )
	  .done(function( data ) {
		  toastr.warning(data);
		  editableLayers.clearLayers();
	  });
	  
	$('#PointSaveModel').modal('hide');
	//var url= "./geohandle.php?pointlat="+latlngs[0]+"&pointname="+"TestName"+"&pointlong="+latlngs[1]+"&reson=insertpoint&user="+userName;
}

function validateAddress(TCode){
    if( /[^a-zA-Z0-9 \-\/]/.test( TCode ) ) {
        //alert('Input is not alphanumeric');
        return false;
    }

    return true;     
}

function ValidateSpaces(TCode)
{
	if(TCode.trim() == "")
	{
		return false;
	}
		return true;
}