@extends('layouts.app')

@section('content')

<div class="panel panel-default" style="border-color:blue">
	<div class="panel-body">
		<div class="tab-content" style="margin-left:0.5em;margin-bottom:0.5em">
			<a href="/bioreactor"><button type="button" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus"></span></button></a>
			&nbsp;&nbsp;
			<a href="/bioreactors/excel"><button type="button" class="btn btn-success btn-sm">Excel&nbsp;<span class="glyphicon glyphicon-download-alt"></span></button></a>
		</div>
		<div class="tab-content">

			<div class="table table-condensed table-responsive">
			<table class="table">
				<thead>
					<tr class="info">
						<th>Edit</th>
						<th>Del</th>
						<th>Name</th>
						<th>Device ID</th>
						<th>City</th>
						<th>Country</th>
						<th>Last Data Sync</th>
						<th>Created On</th>
						<th>Last Updated</th>
					</tr>
				</thead>
				<tbody>
				@foreach ($dbdata as $bioreactor)
					<tr>
					    <td><a href="/bioreactor/{{ $bioreactor->id }}"><button type="button" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-pencil"></span></button></a></td>
						<td><a href="/bioreactor/delete/{{ $bioreactor->id }}"><button type="button" class="btn btn-success btn-xs" onClick='return(deleteOkPrompt("{{ $bioreactor->name }}","{{ $bioreactor->deviceid }}"))'><span class="glyphicon glyphicon-remove"></span></button></a></td>
						<td>{{ $bioreactor->deviceid }}</td>
						<td>{{ $bioreactor->name }}</td>
						<td>{{ $bioreactor->city }}</td>
						<td>{{ $bioreactor->country }}</td>
						<td>{{ $bioreactor->last_datasync_at }}</td>

						<td>{{ $bioreactor->created_at }}</td>
						<td>{{ $bioreactor->updated_at }}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
			</div>

		</div>
	</div>
</div>
@stop

@section('footer_js')
<script type="text/javascript">
function deleteOkPrompt(name, deviceid)
{
	if (confirm('Are you sure you want to delete this Bioreactor?\nName ['+name+']\nDevice ID ['+deviceid+']')) {
		// do it!
		return true;
	} else {
	    // Do nothing!
		return false;
	}
	return false;
}
</script>
@stop
