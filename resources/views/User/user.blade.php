@extends('layouts.app')

@section('content')

<div class="panel panel-default" style="border-color:blue;width:600px">
	<div class="panel-body">


{!! Form::model($user, array('class' => 'form')) !!}

{!! Form::hidden('id', null) !!} 

<div class="form-group">
    {!! Form::label('Name') !!}
    {!! Form::text('name', null, 
        array('required', 
              'class'=>'form-control', 
              'placeholder'=>'Name')) !!}
</div>

<div class="form-group">
    {!! Form::label('E-mail Address') !!}
    {!! Form::text('email', null, 
        array('required', 
              'class'=>'form-control', 
              'placeholder'=>'E-mail address')) !!}
</div>

<div class="form-group">
    {!! Form::label('Administrator') !!}
	{!! Form::checkbox('isadmin') !!}
</div>

<div class="form-group">
    {!! Form::label('BioReactor') !!}
	{!! Form::select('deviceid', array('00001' => '00001 St Paul High School', '00002' => '00002 University of Calgary', '00003' => '00003 Elmira High School') ) !!}
</div>

<div class="form-group">
    {!! Form::submit('Save', 
      array('class'=>'btn btn-primary')) !!}
	  &nbsp;
<a href="/users">
    {!! Form::button('Cancel', 
      array('class'=>'btn')) !!}
</a>
</div>
{!! Form::close() !!}

	</div>
</div>

@stop
