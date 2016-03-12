@extends('layouts.app')

@section('content')

<div class="panel panel-success">
   <div class="panel panel-primary">

	@include('common_detail_header', array('show_map' => false))

	<div class="panel-body">

	<ul class="nav nav-pills">
		<li class="active"><a data-toggle="pill" href="#temp_graph">Graph</a></li>
		<li><a data-toggle="pill" href="#temp_list">Data Point List</a></li>
	</ul>
 
	<div class="tab-content">

		<div id="temp_graph" class="tab-pane fade in active">
			<div style="height:3px"></div>
			<div>Ending at: {{ $end_datetime }}</div>
			<div style='width:500px'>
			   <canvas id="temp_canvas"></canvas>
			</div>
		</div>
		<div id="temp_list" class="tab-pane fade">
			<div style="height:3px"></div>	
			<div class="table table-condensed table-responsive" style='overflow-y:scroll;height:275px'>          
				<table class="table">
					<thead>
						<tr class="info">
							<th>Date and Time</th>
							<th>Temperature</th>
						</tr>
					</thead>
					<tbody>
					@foreach ($dbdata as $temperature)
						<tr>
							<td>{{ $temperature->recorded_on }}</td>
							<td>{{ $temperature->temperature }}</td>
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>

  <script type="text/javascript">

	var temp_lineChartData = {
		labels: [@foreach ($x_temperature_data as $pt)"{{ $pt }}",@endforeach],
		datasets: [{
			fillColor: "rgba(220,220,220,0)",
			strokeColor: "rgba(220,180,0,1)",
			pointColor: "rgba(220,180,0,1)",
			data: [@foreach ($y_temperature_data as $pt)"{{ $pt }}",@endforeach]
		}]
	}

	Chart.defaults.global.animationSteps = 50;
	Chart.defaults.global.tooltipYPadding = 16;
	Chart.defaults.global.tooltipCornerRadius = 0;
	Chart.defaults.global.tooltipTitleFontStyle = "normal";
	Chart.defaults.global.tooltipFillColor = "rgba(0,160,0,0.8)";
	Chart.defaults.global.animationEasing = "easeOutBounce";
	Chart.defaults.global.responsive = true;
	Chart.defaults.global.scaleLineColor = "black";
	Chart.defaults.global.scaleFontSize = 12;

	var ctx = document.getElementById("temp_canvas").getContext("2d");

	var LineChartDemo = new Chart(ctx).Line(temp_lineChartData, {
		pointDotRadius: 5,
		bezierCurve: false,
		scaleShowVerticalLines: false,
		scaleGridLineColor: "black"
	});
  </script>

@stop

