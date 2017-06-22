@extends('layouts.app')

@section('content')

<div class="panel panel-primary">

  @include('common_detail_header', array('show_map' => true, 'show_excel' => $show_excel))

  <div class="panel-body">
    <div class="row" id="sensor-list">
@php
function clearForBlock($seq)
{
    $classes = "clearfix";
    if($seq % 4 === 0) {
      $classes .= " visible-sm-block";
    }
    if($seq % 6 === 0) {
      $classes .= " visible-lg-block";
    }
    return $classes;
}
$i = -1;
@endphp
@foreach ($sensors as $sensor_name => $sensor)
@php ($i++)
@if(($i > 0 )&&(( $i % 4 === 0 )||( $i % 6 === 0)))
      <div class="{{ clearForBlock($i) }}"><!-- {{ $i }} --></div>
@endif
      <div class="col-sm-3 col-lg-2">
        <h4>{{ $sensor['title'] }}</h4>
        <a href='#' data-toggle="modal" data-target="#{{ $sensor_name }}_modal"><canvas id="{{ $sensor_name }}_canvas"></canvas></a>
@if($show_button)
        <div class="row small-gutter">
          <div class="col-sm-4">
            <a class="btn-success btn-xs" href="{{ $sensor['route'] }}">@lang('bioreactor.recent_3_hours')</a>
          </div>
          <div class="col-sm-4">
            <a class="btn-success btn-xs" href="{{ $sensor['route'] }}/24">@lang('bioreactor.recent_1_day')</a>
          </div>
          <div class="col-sm-4">
            <a class="btn-success btn-xs" href="{{ $sensor['route'] }}/168">@lang('bioreactor.recent_1_week')</a>
          </div>
        </div>
@endif
      </div>
@endforeach
    </div>

@if ( isset($show_graph) && $show_graph )
    <div class="row">
      <h4 class="text-center">{{ Lang::get( 'bioreactor.graph_form_title' ) }}</h4>
      {!! Form::open(array('action' => array('GlobalController@formgraph', $id), 'name' => 'graph_options')) !!}
        <div class="col-sm-4 col-md-3 col-lg-3">
          <fieldset>
            <legend class="text-center">{{ Lang::get( 'bioreactor.graph_type_legend' ) }}</legend>
@php ($i = 0)
@foreach ($sensors as $sensor_name => $sensor)
@php ($i++)
            <div class="row">
              <div class="col-sm-12">
                {!! Form::radio( 'sensor_to_graph', $sensor_name, $sensor_name == 'oxygen', array( 'id' => $sensor_name . '_graph' )) !!}
                {!! Form::label( $sensor_name . '_graph', Lang::get( 'export.' . $sensor_name . '_select' )) !!}
              </div>
            </div>
@endforeach
          </fieldset>
        </div>
        <div class="col-sm-2">
          <fieldset>
            <legend class="text-center">{{ Lang::get( 'bioreactor.interval_legend' ) }}</legend>
            <div class="row">
              <div class="col-sm-12">
                {!! Form::radio( 'graph_interval', '3', true, array( 'id' => 'int_3_hrs' )) !!}
                {!! Form::label( 'int_3_hrs', Lang::get( 'bioreactor.interval_3_hours' )) !!}
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                {!! Form::radio( 'graph_interval', '24', false, array( 'id' => 'int_24_hrs' )) !!}
                {!! Form::label( 'int_24_hrs', Lang::get( 'bioreactor.interval_1_day' )) !!}
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                {!! Form::radio( 'graph_interval', '168', false, array( 'id' => 'int_168_hrs' )) !!}
                {!! Form::label( 'int_168_hrs', Lang::get( 'bioreactor.interval_1_week' )) !!}
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                {!! Form::radio( 'graph_interval', 'custom', false, array( 'id' => 'int_input_hrs' )) !!}
                {!! Form::label( 'int_input_hrs', Lang::get( 'bioreactor.interval_custom' )) !!}
              </div>
            </div>
          </fieldset>
        </div>
        <div class="col-sm-6 col-md-4">
          <fieldset>
            <legend>&nbsp;</legend>
            <div class="row">
              <div class="col-sm-12">
                {!! Form::label('hours', Lang::get( 'bioreactor.custom_interval' )) !!}
                {!! Form::number('hours', null, array('optional', 'placeholder' => '3', 'style' => 'width: 5em;')) !!}
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                {!! Form::label('graph_end_date', Lang::get( 'export.enter_end_date' )) !!}
                {{-- Carbon is way more flexible than any of the html date and time input types.  Just use plain text, and let laravel handle it --}}
                {!! Form::text('graph_end_date', \Carbon\Carbon::now(), array('style' => 'width: 100%;')) !!}
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                {{ Form::hidden('utc_time_now', \Carbon\Carbon::now()->format('Y-m-d\TH:iO')) }}
                {{ Form::hidden('timezone_offset', 'tz0') }}
                {!! Form::submit(Lang::get( 'bioreactor.graph_submit' ), array('class'=>'btn btn-success btn-sm','name'=>'submit_graph')) !!}
              </div>
            </div>
          </fieldset>
        </div>
      {!! Form::close() !!}
    </div>
@endif
  </div>
@if ( isset($show_graph) && $show_graph )
  <div class="panel-footer">
@if (count($errors) > 0)
    <h4>{{ Lang::get( 'bioreactor.bad_grahp_select' ) }}</h4>
    <ul class="alert alert-danger">
@foreach ($errors->all() as $er)
      <li>{{ $er }}</li>
@endforeach
    </ul>
@endif
  </div>
@endif
</div>

@stop

@section('modal_insert')
@if($show_excel)
<div class="modal fade modal-dialog modal-content" id="raw_data_export_modal" role="dialog">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">@lang('export.raw_to_spreadsheet_title')</h4>
  </div>
  <div class="modal-body">

    {!! Form::open(array('url' => '/export', 'name' => 'data_export')) !!}
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
                {!! Form::label('start_date', Lang::get( 'export.enter_start_date' )) !!}
                {!! Form::date('start_date', \Carbon\Carbon::now()) !!}
              </td>
              <td>
                {!! Form::label('end_date', Lang::get( 'export.enter_end_date' )) !!}
                {!! Form::date('end_date', \Carbon\Carbon::now()) !!}
              </td>
            </tr>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        {{ Form::hidden('timezone_offset', 'tz0') }}
        {!! Form::submit(Lang::get( 'export.start_export' ), array('class'=>'btn btn-success btn-sm', 'name' => 'submit_export')) !!}
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    {!! Form::close() !!}
@if (count($errors) > 0)
      <div class="alert alert-danger">
        <ul>
@foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
@endforeach
        </ul>
      </div>
@endif
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
@if(stream_resolve_include_path('MyBio.common_' . $sensor_name . '_charts'))
@include('MyBio.common_' . $sensor_name . '_charts')
@endif
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

    // Fill in user (actually browser) timezone information, to help out with
    // generic date values.
    // TODO to do this **properly** need the offset for the actual entered date
    //  values (pre submit), since they could be on different sides of a
    //  daylight savings change
    // TODO or even convert to UTC here, so server does not need to consider time zones
    $("[name='timezone_offset']").each(function(idx){
      $(this)["value"] = (new Date()).getTimezoneOffset();
    })

    $("[name='graph_end_date']")[0]["value"] = bin.fmtLocalDateTime(new Date($("[name='utc_time_now']")[0]['value']));
}());// anonymous function()
</script>

@include('common_single_map')
@stop
