@extends('layouts.app')

@section('content')

<div class="panel panel-primary">

  @include('common_detail_header', array('show_map' => true, 'show_excel' => $show_excel))

  <div class="panel-body navbar-collapse">
    <ul class="nav navbar-nav" id="sensor-list">
@foreach ($sensors as $sensor_name => $sensor)
      <li>
        <h4>{{ $sensor['title'] }}</h4>
        <a href='#' data-toggle="modal" data-target="#{{ $sensor_name }}_modal"><canvas id="{{ $sensor_name }}_canvas"></canvas></a>
@if($show_button)
        <div>
          <a class="btn-success btn-xs" href="{{ $sensor['route'] }}">@lang('bioreactor.recent_3_hours')</a>
          <a class="btn-success btn-xs" href="{{ $sensor['route'] }}/24">@lang('bioreactor.recent_1_day')</a>
          <a class="btn-success btn-xs" href="{{ $sensor['route'] }}/168">@lang('bioreactor.recent_1_week')</a>
        </div>
@endif
      </li>
@endforeach
    </ul>
  </div>
</div>

@if($show_excel)
<div class="modal fade modal-dialog modal-content" id="raw_data_export_modal" role="dialog">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">@lang('export.raw_to_spreadsheet_title')</h4>
  </div>
  <div class="modal-body">

    {!! Form::open(array('url' => '/export')) !!}
      <div class="form-group">
        <div class="table table-condensed table-responsive">
          <table class="table">
            <tr class="info">
@foreach ($sensors as $sensor_name => $sensor)
              <td>
                {!! Form::label( $sensor_name . '_readings', Lang::get( 'export.' . $sensor_name . '_select' )) !!}
                {!! Form::radio( 'datatype_to_excel', $sensor_name, $sensor_name == 'oxygen', array( 'id' => $sensor_name . '_readings' )) !!}
              </td>
@endforeach
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
@endif

@foreach ($sensors as $sensor_name => $sensor)
@include('Global.sensor_graph')
@endforeach

@stop

@section('footer_js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.bundle.min.js"></script>

{{-- without specifying $sensor_name :: will be last from previous foreach --}}
@include('common_line_chart')
@foreach ($sensors as $sensor_name => $sensor)
@include('MyBio.common_' . $sensor_name . '_charts')
@endforeach

<script>
/*global $*/
/*jslint browser, devel */

(function () {
    "use strict";
    var base = document.com.solarbiocells.biomonitor;
    var bin = base.bin;

    // Populate each of the small graph canvases
@foreach ($sensors as $sensor_name => $sensor)
    base.{{ $sensor_name }}Points = [@foreach ($sensor['xy_data'] as $pt){x: {{ $pt['x'] }}000, y: {{ $pt['y'] }}}@if ($pt !== end($sensor['xy_data'])), @endif{{-- --}}@endforeach];
    bin.populateScatterChart("{{ $sensor_name }}_canvas", "small", "{{ $sensor_name }}", base.{{ $sensor_name }}Points);
@endforeach

    // Populate each of the big graph canvases after the rest of the document loads
@foreach ($sensors as $sensor_name => $sensor)
    $(document).ready(function () {
        $("#{{ $sensor_name }}_modal").on("shown.bs.modal", function () {
            bin.populateScatterChart("big_{{ $sensor_name }}_canvas", "big", "{{ $sensor_name }}", base.{{ $sensor_name }}Points);
        });
    });
@endforeach
}());// anonymous function()
</script>

@include('common_single_map')
@stop
