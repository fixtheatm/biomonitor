@extends('layouts.app')

@section('content')

<div class="panel panel-primary" style="width:600px">
	<div class="panel-heading">
	 {{ $header_title }}
	</div>

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

@if ($user->id < '1')
<div class="form-group">
    {!! Form::label('Password') !!}
    {!! Form::text('password', null,
        array('required',
              'class'=>'form-control',
              'placeholder'=>'Password')) !!}
</div>
@endif

<div class="form-group">
    {!! Form::label('Administrator') !!}
	{!! Form::checkbox('isadmin') !!}
</div>

<div class="form-group">
    {!! Form::label('BioReactor') !!}
	{!! Form::select('deviceid', $bioreactors ) !!}
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
