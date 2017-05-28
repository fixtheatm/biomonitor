@extends('layouts.app')

@section('content')
<div class="panel panel-primary">

  @include('common_detail_header', array('show_map' => true))

  <div class="panel-body collapse navbar-collapse">
    <ul class="nav navbar-nav" id="sensor-list">
@foreach ($sensors as $sensor)
      <li>
        <h4>{{ $sensor['title'] }}</h4>
        <a href='#' data-toggle="modal" data-target="#{{ $sensor['name'] }}_modal"><canvas id="{{ $sensor['name'] }}_canvas"></canvas></a>
      </li>
@endforeach
    </ul>
  </div>
</div>

@each('Global.sensor_graph', $sensors, 'sensor')

@stop

@section('footer_js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

@include('common_line_chart')
@include('GasFlows.common_gasflow_charts')
@include('LightReadings.common_lightreading_charts')
@include('Temperatures.common_temperature_charts')
@include('PhReadings.common_phreading_charts')

@each('Global.sensor_graph_js', $sensors, 'sensor')

@include('common_single_map')

@stop
