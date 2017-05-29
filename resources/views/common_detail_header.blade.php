  <div class="panel-heading">
    <div class="row">
      <div class="col-sm-4">
         @lang('export.bioreactor_name_label')<b>{{ $bioreactor->name }}</b><br>@lang('export.bioreactor_city_label')<b>{{ $bioreactor->city }}</b><br>@lang('export.bioreactor_cntry_label')<b>{{ $bioreactor->country }}</b><br>
      </div>
      <div class="col-sm-3">
        @lang('export.bioreactor_id_label')<b>{{ $bioreactor->deviceid }}</b><br>
        @lang('export.bioreactor_email_label')<b>{{ $bioreactor->email }}</b><br>
      </div>
@if ( isset($show_map) && $show_map )
      <div class="col-sm-2">
        <div id="map_canvas" style="width:120px;height:120px"></div>
      </div>
@endif
@if ( isset($show_excel) && $show_excel )
      <div class="col-sm-2" style="margin-left:20px">
        <a class="btn btn-success btn-sm" href='#' data-toggle="modal" data-target="#raw_data_export_modal">
         @lang('export.raw_to_spreadsheet_btn') <span class="glyphicon glyphicon-download-alt"></span>
        </a>
      </div>
@endif
    </div>
  </div>
