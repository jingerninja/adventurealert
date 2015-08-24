@extends('app')

@section('content')

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-10 col-sm-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="row">
						<div class="col-sm-6 panel-title">Create a Contact</div>
						<div class="pull-right" style="margin-right: 30px;">
							<button class="btn btn-success" type="submit" form="createEmergencyContact"><i class="fa fa-floppy-o"></i> Save</button>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<div class="row">
						@include('errors.list')

						{!! Form::open(['url' => '/contacts', 'id' => 'createEmergencyContact']) !!}
							@include('contacts.form')
						{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection