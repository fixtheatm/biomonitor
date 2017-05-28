@extends('layouts.app')

@section('content')

<div class="panel panel-primary">

  @include('common_detail_header', array('show_map' => true, 'show_excel' => true))

  <div class="panel-body navbar-collapse">
    <ul class="nav navbar-nav" id="sensor-list">
@foreach ($sensors as $sensor)
      <li>
        <h4>{{ $sensor['title'] }}</h4>
        <a href='#' data-toggle="modal" data-target="#{{ $sensor['name'] }}_modal"><canvas id="{{ $sensor['name'] }}_canvas"></canvas></a>
        <div>
          <a class="btn-success btn-xs" href="/my{{ $sensor['graph'] }}s">3 Hours</a>
          <a class="btn-success btn-xs" href="/my{{ $sensor['graph'] }}s/24">24 Hours</a>
        </div>
      </li>
@endforeach
    </ul>
  </div>
</div>

<div class="modal fade" id="raw_data_export_modal" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Raw Data Export to Excel</h4>
      </div>
      <div class="modal-body">
        {!! Form::open(array('url' => '/export')) !!}

        <div class="form-group">
          <div class="table table-condensed table-responsive">
            <table class="table">
              <tr class="info">
                <td>
                  {!! Form::label('gasflow_readings', 'Gas Flow Readings ') !!}
                  {!! Form::radio('datatype_to_excel', 1, true, array('id'=>'gasflow_readings')) !!}
                </td>
                <td>
                  {!! Form::label('light_readings') !!}
                  {!! Form::radio('datatype_to_excel', 2, false, array('id'=>'light_readings')) !!}
                </td>
                <td>
                  {!! Form::label('temp_readings', 'Temperature Readings ') !!}
                  {!! Form::radio('datatype_to_excel', 3, false, array('id'=>'temp_readings')) !!}
                </td>
                <td>
                  {!! Form::label('ph_readings', 'pH Readings') !!}
                  {!! Form::radio('datatype_to_excel', 2, false, array('id'=>'ph_readings')) !!}
                </td>
              </tr>
            </table>
          </div>
          <div class="table table-condensed table-responsive">
            <table class="table">
              <tr class="info">
                <td>
                  {!! Form::label('start_date') !!}
                  {!! Form::date('start_date', \Carbon\Carbon::now()) !!}
                </td>
                <td>
                  {!! Form::label('end_date') !!}
                  {!! Form::date('end_date', \Carbon\Carbon::now()) !!}
                </td>
              </tr>
            </table>
          </div>
        </div>

        <div class="modal-footer">
          {!! Form::submit('Go', array('class'=>'btn btn-success btn-sm')) !!}
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        {!! Form::close() !!}
      </div>
    </div>
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
