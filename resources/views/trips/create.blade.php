@extends('app')

@section('content')

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-7 col-sm-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="row">
						<div class="col-sm-6 panel-title">Create a Trip Plan</div>
						<div class="pull-right" style="margin-right: 30px;">
							<button class="btn btn-success" type="submit" form="createTripPlan"><i class="fa fa-floppy-o"></i> Save</button>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<div class="row">
						@include('errors.list')

						{!! Form::open(['url' => '/trips', 'id' => 'createTripPlan']) !!}
							@include('trips.form')
						{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-3 alert-info">
			<h2 class="text-center"><i class="fa fa-exclamation-circle"></i> Attention</h2>
			<p>If you do not check back in from this trip, your contacts will be notified on:</p>
			<h4 id="notifyTime" class="text-center"></h4>
		</div>
	</div>
</div>

@stop

@section('scripts')

	<script type="text/javascript">
		$("#end_time, #alert_timer").on("dp.change", function (e) {
            var notifyTime = moment(new Date($('#end_time').val()));
            var alertTimer = $('#alert_timer').val();
            alertTimer = alertTimer.split(':');
            notifyTime.add(alertTimer[0],'hours');
            notifyTime.add(alertTimer[1], 'minutes');

            $("#notifyTime").html(notifyTime.format("dddd, MMM Do @ h:mm A"));
        });
	</script>

@append 