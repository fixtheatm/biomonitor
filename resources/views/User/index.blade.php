@extends('layouts.app')

@section('content')

<div class="panel panel-default" style="border-color:blue">
	<div class="panel-body">
		<div style="margin-left:8px;margin-bottom:5px">
			<a href="/user"><button type="button" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus"></span></button></a>
		</div>
		<div class="tab-content">

			<div class="table table-condensed table-responsive">          
			<table class="table">
				<thead>
					<tr class="info">
						<th>Edit</th>
						<th>Name</th>
						<th>Email</th>
						<th>Device ID</th>
						<th>Admin</th>
						<th>Created On</th>
						<th>Last Updated</th>
					</tr>
				</thead>
				<tbody>
				@foreach ($dbdata as $user)
					<tr>
					    <td><a href="/user/{{ $user->id }}"><button type="button" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-pencil"></span></button></a></td>
						<td>{{ $user->name }}</td>
						<td>{{ $user->email }}</td>
						<td>{{ $user->deviceid }}</td>

						<td>@if ( $user->isadmin ) Yes @else &nbsp; @endif </td>
						<td>{{ $user->created_at }}</td>
						<td>{{ $user->updated_at }}</td>
					</tr>
				@endforeach
				</tbody>
			</table>


		</div>
	</div>
</div>
@stop
