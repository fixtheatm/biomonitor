@extends('layouts.app')

@section('content')

<div class="panel panel-primary" style="width:600px">
	<div class="panel-heading">
	 {{ $header_title }}
	</div>

	<div class="panel-body">


{!! Form::model($bioreactor, array('class' => 'form')) !!}

{!! Form::hidden('id', null) !!}

@if ($bioreactor->id < '1')
<div class="form-group">
    {!! Form::label('Device ID') !!}
    {!! Form::text('deviceid', null,
        array('required',
              'class'=>'form-control',
              'placeholder'=>'Device ID')) !!}
</div>
@endif

<div class="form-group">
    {!! Form::label('Name') !!}
    {!! Form::text('name', null,
        array('required',
              'class'=>'form-control',
              'placeholder'=>'Name')) !!}
    {!! Form::label('City') !!}
    {!! Form::text('city', null,
        array('required',
              'class'=>'form-control',
              'placeholder'=>'City')) !!}
    {!! Form::label('Country') !!}
    {!! Form::text('country', null,
        array('required',
              'class'=>'form-control',
              'placeholder'=>'Country')) !!}
</div>

<div class="form-group">
    {!! Form::label('Latitude') !!}
    {!! Form::text('latitude', null,
        array('required',
              'class'=>'form-control',
              'placeholder'=>'Latitude')) !!}

    {!! Form::label('Longitude') !!}
    {!! Form::text('longitude', null,
        array('required',
              'class'=>'form-control',
              'placeholder'=>'Longitude')) !!}
</div>

<div class="form-group">
    {!! Form::label('System E-mail Address') !!}
    {!! Form::text('email', null,
        array('required',
              'class'=>'form-control',
              'placeholder'=>'E-mail address')) !!}
</div>


<div class="form-group">
    {!! Form::submit('Save',
      array('class'=>'btn btn-primary')) !!}
	  &nbsp;
<a href="/bioreactors">
    {!! Form::button('Cancel',
      array('class'=>'btn')) !!}
</a>
</div>
{!! Form::close() !!}

	</div>
</div>

@stop
