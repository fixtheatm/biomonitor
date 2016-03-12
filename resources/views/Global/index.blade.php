@extends('layouts.app')

@section('content')

<div class="panel panel-default" style="border-color:blue">
<div class="panel-body">

<ul class="nav nav-pills">
  <li class="active"><a data-toggle="pill" href="#global_map">Map</a></li>
  <li><a data-toggle="pill" href="#global_list">List</a></li>
</ul>
 
<div class="tab-content">
  <div id="global_map" class="tab-pane fade in active">
    <div style="height:3px"></div>
	<div class="container-fluid" style="height:400px;width:100%">
	  <div id="map_canvas" style="height:100%"></div>
	</div>
  </div>
  <div id="global_list" class="tab-pane fade">
    <div style="height:3px"></div>
	<div style="width:500p">
		<div class="table table-condensed table-responsive">          
			<table class="table">
				<thead>
					<tr class="info">
						<th>View</th>
						<th>ID</th>
						<th>Name</th>
						<th>City</th>
						<th>Country</th>
					</tr>
				</thead>
				<tbody>
				@foreach ($dbdata as $bioreactor)
					<tr>
					    <td><a href="/single/{{ $bioreactor->deviceid }}"><button type="button" class="btn btn-success btn-xs">Go</button></a></td>
						<td>{{ $bioreactor->deviceid }}</td>
						<td>{{ $bioreactor->name }}</td>
						<td>{{ $bioreactor->city }}</td>
						<td>{{ $bioreactor->country }}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
	</div>
	   
  </div>
</div>

</div>
</div>
@stop

@section('footer_js')
  <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
  <script type="text/javascript" src="/js/ui/jquery.ui.map.js"></script>

	<script type="text/javascript">
    $('#map_canvas').gmap().bind('init', function() { 
	$.getJSON( '/getjson', function(data) { 

		$.each( data.markers, function(i, marker) {

		    var lat = parseFloat(marker.latitude);
		    var long = parseFloat(marker.longitude);
			var href = '/single/' + marker.deviceid;
			var butcontent = marker.name + '&nbsp;<a href="'+href+'"><button type="button" class="btn btn-success btn-xs">Go</button></a>';

			$('#map_canvas').gmap('addMarker', { 
				'position': new google.maps.LatLng(lat, long), 
				'bounds': true 
			}).click(function() {
				$('#map_canvas').gmap('openInfoWindow', { 'content': butcontent }, this);
			});
		});
	});
});

	</script>

@stop

