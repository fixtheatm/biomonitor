@extends('layouts.app')

@section('content')

<div class="panel panel-default" style="border-color:blue;width:600px">
	<div class="panel-body">

	<h3>{{ $user->name }}</h3>
	<h3>{{ $user->email }}</h3>
	Password must be at least 6 characters long, have at least 1 number, 1 uppercase letter and 1 lower case letter. Use
	only letters, numbers and underscores.<br><br>

{!! Form::model($user, array('class' => 'form', 'name' => 'ChangePassword')) !!}

<input name="GoBackTo" type="hidden" value="{{ URL::previous() }}">

<div class="form-group">
    {!! Form::label('New Password') !!}
    {!! Form::password('password1', array('id' => 'password1')) !!}
</div>

<div class="form-group">
    {!! Form::label('Repeat Password') !!}
    {!! Form::password('password2', array('id' => 'password2')) !!}
</div>

<div class="form-group">
    {!! Form::submit('Save',
      array('class'=>'btn btn-primary', 'onClick' => 'return (passwordChecker())' )) !!}
	  &nbsp;
<a href="{{ URL::previous() }}">
    {!! Form::button('Cancel',
      array('class'=>'btn')) !!}
</a>
</div>
{!! Form::close() !!}

	</div>
</div>

@stop

@section('footer_js')
<script type="text/javascript">
function passwordChecker()
{
    pwdelem = document.getElementById("password1");

	pass1 = document.getElementById("password1").value;
	pass2 = document.getElementById("password2").value;

	if ( pass1 != pass2)
	{
 		alert('Password entries do not match');
        pwdelem.focus();
		return false;
	}
	if (pass1.length < 6)
	{
 		alert('Sorry! Password must be at least 6 characters long');
        pwdelem.focus();
		return false;
	}
	re = /^\w+$/;
    if(!re.test(pass1)) {
        alert("Sorry! Password can only contain letters, numbers and underscores!");
        pwdelem.focus();
        return false;
    }
	re = /[0-9]/;
    if(!re.test(pass1)) {
        alert("Sorry! Password must contain at least one number (0-9)!");
        pwdelem.focus();
        return false;
    }
    re = /[a-z]/;
    if(!re.test(pass1)) {
        alert("Sorry! Password must contain at least one lowercase letter (a-z)!");
        pwdelem.focus();
        return false;
    }
    re = /[A-Z]/;
    if(!re.test(pass1)) {
        alert("Sorry! Password must contain at least one uppercase letter (A-Z)!");
        pwdelem.focus();
        return false;
    }
	return true;
}

</script>
@stop
