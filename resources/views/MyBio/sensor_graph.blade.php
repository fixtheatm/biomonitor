@extends('layouts.app')

@section('content')

<div class="panel panel-success">
<div class="panel panel-primary">

  @include('common_detail_header', array('show_map' => false))

  <div class="panel-body">
    <ul class="nav nav-pills">
      <li class="active"><a data-toggle="pill" href="#data_graph">@lang('bioreactor.graph_btn')</a></li>
      <li><a data-toggle="pill" href="#data_list">@lang('bioreactor.data_pt_list_btn')</a></li>
    </ul>

    <div class="tab-content">
      <div id="data_graph" class="tab-pane fade in active">
        <div style="height:3px"></div>
        <div>
           <canvas id="chart_canvas"></canvas>
        </div>
      </div>
      <div id="data_list" class="tab-pane fade">
        <div style="height:3px"></div>
        <div class="table table-condensed table-responsive">
          <table class="table table-fixed">
            <thead>
              <tr class="info">
                <th class="col-xs-8">@lang('bioreactor.date_time_head')</th>
                <th class="col-xs-4">{{ $value_label }}</th>
              </tr>
            </thead>
            <tbody>
<!-- { { $measurement }} -->
@foreach ($dbdata as $measurement)
              <tr>
                <td class="col-xs-8">{{ $measurement->recorded_on }}</td>
                <td class="col-xs-4">{{ $measurement[$value_field] }}</td>
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
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.bundle.min.js"></script>

@include('common_line_chart')
<!--
var lineChartTemplate = {
    fill: false,
    lineTension: 0,
    spanGaps: false,
    borderColor: "rgba(220,180,0,1)",
    pointBackgroundColor: "rgba(220,180,0,1)",
    pointHoverBackgroundColor: "rgba(75,192,192,1)",
    pointHoverBorderColor: "rgba(220,0,0,1)"
}; -->
@include('GasFlows.common_gasflow_charts')
@include('LightReadings.common_lightreading_charts')
@include('Temperatures.common_temperature_charts')
@include('PhReadings.common_phreading_charts')


<script>
// Get the actual sensor values
var sensorValues = [@foreach ($y_data as $pt)"{{ $pt }}",@endforeach];

var sensorDataSet = [ $.extend({}, lineChartTemplate, {
    data: sensorValues,
    pointRadius: 5,
    pointHoverRadius: 12,
    pointHoverBorderWidth: 2
})];
var sensorChartData = {
    labels: [@foreach ($x_data as $pt)"{{ $pt }}",@endforeach],
    datasets: sensorDataSet
};
// alert ("full gas flow:"+JSON.stringify(full_{{ $sensor_name }}Options));
var ctx = document.getElementById("chart_canvas").getContext("2d");
var sensorGraph = new Chart( ctx, {
    type: "line",
    data: sensorChartData,
    options: full_{{ $sensor_name }}Options
});

</script>

@stop
