@extends('layouts.app')

@section('content')

<div class="panel panel-primary">

  @include('common_detail_header', array('show_map' => false))

  <div class="panel-body">
    <ul class="nav nav-pills">
      <li class="active"><a data-toggle="pill" href="#data_graph">@lang('bioreactor.graph_btn')</a></li>
      <li><a data-toggle="pill" href="#data_list">@lang('bioreactor.data_pt_list_btn')</a></li>
    </ul>

    <div class="tab-content">
      <div id="data_graph" class="tab-pane fade in active">
         <canvas id="chart_canvas"></canvas>
      </div>
      <div id="data_list" class="tab-pane fade">
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

@stop


@section('footer_js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.bundle.min.js"></script>
<!--
route ¦{{ $route }}¦
sensor_name ¦{{ $sensor_name }}¦
value_field ¦{{ $value_field }}¦
value_label ¦{{ $value_label }}¦
id ¦{{ $id }}¦
bioreactor name ¦{{ $bioreactor['name'] }}¦
end_datetime ¦{{ $end_datetime }}¦
point_count ¦{{ $point_count }}¦
interval_count ¦{{ $interval_count }}¦
point count xy {{ count( $xy_data ) }}
dbdata count {{ count( $dbdata ) }} -->

@include('common_line_chart')

<!-- get the pieces to use for the current sensor type -->
<!--
  sensor_view ¦{{ $sensor_view }}¦
  sensor_type ¦{{ $sensor_type }}¦
  @@include('{{ $sensor_view }}.common_{{ $sensor_type }}_charts') -->
@include($sensor_view . '.common_' . $sensor_type . '_charts')

<script>

var sensorDataSet = [ $.extend( true, {}, baseDataset, fullDataset, {
  data: graphPoints,
})];
// full_{{ $sensor_name }}Options
var graphOptions = $.extend( true, {}, baseOptions, fullOptions, {{ $sensor_name }}Options);

var ctx = document.getElementById("chart_canvas").getContext("2d");
var sensorGraph = new Chart( ctx, {
    type: 'scatter',
    data: {
        datasets: sensorDataSet
    },
    options: graphOptions
});

</script>

@stop
