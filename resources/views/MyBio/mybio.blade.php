@extends('layouts.app')

@section('content')

<div class="panel panel-primary">

	@include('common_detail_header', array('show_map' => true))

	<div class="panel-body">

		<div class="table table-condensed table-responsive">          
			<table class="table">
				<thead>
					<tr class="info">
						<th>Gas Flow</th>
						<th>Light</th>
						<th>Temperature</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><a href='#' data-toggle="modal" data-target="#gasflow_modal"><div style='width:180px'><canvas id="gasflow_canvas"></div></canvas></a></td>
						<td><a href='#' data-toggle="modal" data-target="#light_modal"><div style='width:180px'><canvas id="light_canvas"></canvas></div></a></td>
						<td><a href='#' data-toggle="modal" data-target="#temperature_modal"><div style='width:180px'><canvas id="temp_canvas"></canvas></div></a></td>
					</tr>
					<tr>
						<td><a href="/mygasflows"><button type="button" class="btn btn-success btn-xs">3 Hours</button></a>
						    <a href="/mygasflows/24"><button type="button" class="btn btn-success btn-xs">24 Hours</button></a></td>
						<td><a href="/mylightreadings"><button type="button" class="btn btn-success btn-xs">3 Hours</button></a>
						    <a href="/mylightreadings/24"><button type="button" class="btn btn-success btn-xs">24 Hours</button></a></td>
						<td><a href="/mytemperatures"><button type="button" class="btn btn-success btn-xs">3 Hours</button></a>
						    <a href="/mytemperatures/24"><button type="button" class="btn btn-success btn-xs">24 Hours</button></a></td>
					</tr>
				</tbody>

			</table>
		</div>
	</div>
</div> 



  <div class="modal fade" id="gasflow_modal" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Gas Flow</h4>
        </div>
        <div class="modal-body">
			<div style='width:500px;height:300px'><canvas id="big_gasflow_canvas"></canvas></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="light_modal" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Light</h4>
        </div>
        <div class="modal-body">
			<div style='width:500px;height:300px'><canvas id="big_light_canvas"></canvas></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="temperature_modal" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Temperature</h4>
        </div>
        <div class="modal-body">
			<div style='width:500px;height:300px'><canvas id="big_temp_canvas"></canvas></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

@stop



@section('footer_js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>

@include('Temperatures.common_temperature_charts')

@include('LightReadings.common_lightreading_charts')

@include('GasFlows.common_gasflow_charts')

@include('common_single_map')



@stop
