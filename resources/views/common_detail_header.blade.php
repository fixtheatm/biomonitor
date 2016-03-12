 <div class="panel-heading">
   <div class="row">
			<div class="col-sm-4">
			 Name: <b>{{ $bioreactor->name }}</b><br>City: <b>{{ $bioreactor->city }}</b><br>Country: <b>{{ $bioreactor->country }}</b><br>
			</div>
			<div class="col-sm-3">
			 ID#:  <b>{{ $bioreactor->deviceid }}</b><br>
			 Email: <b>{{ $bioreactor->email }}</b><br>
			</div>
			@if ( isset($show_map) && $show_map )
			<div class="col-sm-5">
			   <div id="map_canvas" style="width:120px;height:120px"></div>
			</div>
			@endif
		</div>	 
	</div>
