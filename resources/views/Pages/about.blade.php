@extends('layouts.app')

@section('content')

<div class="panel panel-success">
  <div class="panel panel-primary">
    <div class="panel-heading">@lang('menus.about_header')</div>
    <div class="panel-body">
      <div class="row">
	      <div class="col-sm-6">
	        <b>Solar BioCells</b><br><b>2500 University Drive NW, EEEL 509</b><br>
	        <b>Calgary, AB</b><br>
	        <b>T2N 1N4</b><br>
	      </div>
	      <div class="col-sm-6">
	        @lang('messages.email')<b>info@solarbiocells.com</b><br>
	        @lang('messages.phone')<b>+1 (403) 220-6604 </b><br>
	      </div>
      </div>
    </div>
   </div>
</div>
@stop
