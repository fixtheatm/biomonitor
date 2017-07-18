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
@foreach ($dbdata as $measurement)
              <tr>
                <td class="col-xs-8">{{ $measurement->recorded_on }} UTC</td>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.min.js"></script>
<!--
value_field ¦{{ $value_field }}¦
value_label ¦{{ $value_label }}¦
id ¦{{ $id }}¦
bioreactor name ¦{{ $bioreactor['name'] }}¦
interval_count ¦{{ $interval_count }}¦
@foreach ($sensors as $sensor_name => $sensor)
sensor name ¦{{ $sensor_name }}¦
end_datetime ¦{{ $sensor['end_datetime'] }}¦
point_count ¦{{ $sensor['point_count'] }}¦
point count xy ¦{{ count( $sensor['xy_data'] ) }}¦
@endforeach
dbdata count {{ count( $dbdata ) }} -->

{{-- only a single entry, but easy way to get the sensor name key --}}
@foreach ($sensors as $sensor_name => $sensor)
@include('common_line_chart')

<!-- get the pieces to use for the current sensor type -->
@include('MyBio.common_' . $sensor_name . '_charts')
@endforeach

<script>
/*global $*/
/*jslint browser, devel */

(function () {
    "use strict";
    var bin = document.com.solarbiocells.biomonitor.bin;
    // The raw data points that will be used with whatever chart is being displayed
@foreach ($sensors as $sensor_name => $sensor)
<? $idx = 0; ?>{{--  add ', ' before each new entry, except for the first --}}
    var graphPoints = [@foreach ($sensor['xy_data'] as $pt)@if ($idx++ !== 0), @endif{x: {{ $pt['x'] }}000, y: {{ $pt['y'] }}}@endforeach];
@endforeach
    bin.populateScatterChart("chart_canvas", "full", "{{ $sensor_name }}", graphPoints);
}());// anonymous function()
</script>

@stop
