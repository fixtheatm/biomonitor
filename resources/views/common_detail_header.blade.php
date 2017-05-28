  <div class="panel-heading">
    <div class="row">
      <div class="col-sm-4">
        Name: <b>{{ $bioreactor->name }}</b><br>City: <b>{{ $bioreactor->city }}</b><br>Country: <b>{{ $bioreactor->country }}</b><br>
      </div>
      <div class="col-sm-3">
        ID#:  <b>{{ $bioreactor->deviceid }}</b><br>
        Email: <b>{{ $bioreactor->email }}</b><br>
      </div>
@if ( isset($show_map) && $show_map )
      <div class="col-sm-2">
        <div id="map_canvas" style="width:120px;height:120px"></div>
      </div>
@endif
@if ( isset($show_excel) && $show_excel )
      <div class="col-sm-2" style="margin-left:20px">
        <a class="btn btn-success btn-sm" href='#' data-toggle="modal" data-target="#raw_data_export_modal">
         Raw Data to Excel&nbsp;<span class="glyphicon glyphicon-download-alt"></span>
        </a>
      </div>
@endif
    </div>
  </div>
