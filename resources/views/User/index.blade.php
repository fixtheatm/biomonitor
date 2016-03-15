@extends('layouts.app')

@section('content')

<div class="panel panel-default" style="border-color:blue">
	<div class="panel-body">

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
					    <td><a href="/user/{{ $user->id }}"><button type="button" class="btn btn-success btn-xs">Edit</button></a></td>
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
