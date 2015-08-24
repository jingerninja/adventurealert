@extends('app')

@section('content')

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-7 col-sm-offset-1">
			@if (Session::has('flash_message'))
				<div class="alert {{ session('alert_class') }}">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					{{ session('flash_message') }}
				</div>
			@endif
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="row">
						<div class="col-sm-9">
							<div class="panel-title">Your Trips</div>
						</div>
						<div class="pull-right" style="margin-right: 20px;">
							<a href="/trips/create" class="btn btn-success">Create a Trip</a>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<div class="col-sm-6">
						@foreach($trips as $trip)
							<div class="col-sm-12">
								<div class="panel panel-default">{{-- write land/sea/air display classes for these boxes --}}
									<div class="panel-heading">
										<div class="row">
											<div class="col-sm-9" role="button" data-toggle="collapse" href="#trip{{$trip->id}}" aria-expanded="false" aria-controls="trip{{$trip->id}}">
												<h3 class="panel-title">
													<i class="fa fa-caret-down"></i> {{$trip->title}}
												</h3>
											</div>
											<div id="hiddenMenu{{$trip->id}}" class="pull-right" style="margin-right: 10px; display: none;">
												<div class="btn-group">
													<button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
														<i class="fa fa-bars"></i>
													</button>
													<ul class="dropdown-menu context-menu">
														<li class="edit"><a href="/trips/{{$trip->id}}/edit"><i class="fa fa-fw fa-pencil"></i></a></li>
														<li class="delete" ><a href="#" data-href="/trips/{{$trip->id}}" data-toggle="modal" data-target="#confirm-delete" data-token="{{ csrf_token() }}"><i class="fa fa-fw fa-trash"></i></a></li>
													</ul>
												</div>
											</div>
										</div>
									</div>
									<div id="trip{{$trip->id}}" class="panel-collapse collapse">
										<div class="panel-body">
											<div class="row">
												<div class="col-sm-8">
													<h5><i class="fa fa-fw fa-lg fa-calendar-o"></i> {{$trip->start_time->format('M d - h:i A')}}</h5>
													<h5><i class="fa fa-fw fa-lg fa-calendar-times-o"></i> {{$trip->end_time->format('M d - h:i A')}}</h5>
												</div>
												<div class="col-sm-4">
													{{-- Display check in when trip inactive, display check out when trip active? --}}
													@if($trip->active == 0)
														<a href="/trips/{{$trip->id}}/checkin" class="btn btn-success btn-checkin">Check In</a>
													@elseif($trip->active == 1)
														<a href="/trips/{{$trip->id}}/checkout" class="btn btn-success btn-checkout">Check Out</a>
													@endif
												</div>
											</div>
											<div class="row">
												<div class="col-sm-12">
													<p>{!! nl2br($trip->description) !!}</p>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-3 alert-success">
			<h2 class="text-center"><i class="fa fa-map-o"></i> Active Trip</h2>
			<hr>
			@forelse($activeTrips as $trip)
			    <div id="activeTrip{{$trip->id}}" class="col-sm-12">
			    	<p>{{$trip->title}}</p>
					<div class="row">
						<div class="col-sm-8">
							<h5><i class="fa fa-fw fa-lg fa-calendar-o"></i> {{$trip->start_time->format('M d - h:i A')}}</h5>
							<h5><i class="fa fa-fw fa-lg fa-calendar-times-o"></i> {{$trip->end_time->format('M d - h:i A')}}</h5>
						</div>
						<div class="col-sm-4">
							<a href="/trips/{{$trip->id}}/checkout" class="btn btn-success btn-checkout">Check Out</a>
						</div>
					</div>
				</div>
			@empty
			    <p>You are not currently checked in to a trip.</p>
			@endforelse
		</div>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content alert-danger">
				<div class="modal-header alert-danger">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Are you sure you want to delete this Trip?</h4>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-danger btn-ok">Delete</button>
				</div>
			</div>
		</div>
	</div>

@endsection

@section('scripts')

	<script type="text/javascript">
		//show context menu when trip expanded
		$("div[id^='trip']").on('show.bs.collapse', function() {
			var tripID = $(this).attr('id').split('trip');
			var tripID = tripID[1];
			$('#hiddenMenu'+tripID).css('display','block');
		});
		//hide content menu when trip collapsed
		$("div[id^='trip']").on('hide.bs.collapse', function() {
			//show context-menu for this trip
			var tripID = $(this).attr('id').split('trip');
			var tripID = tripID[1];
			$('#hiddenMenu'+tripID).css('display','none');
		});

		//delete trip
		/* kind of working, would still like to know how to turn the ajax fail into a nice flashed error message... */
		$('#confirm-delete').on('show.bs.modal', function(e) {
			$(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
			var url = $(this).find('.btn-ok').attr('href');
			$(this).find('.btn-ok').attr('data-token', $(e.relatedTarget).data('token'));
			var data = {_token: $(this).find('.btn-ok').attr('data-token')};

			$(this).find('.btn-ok').click(function(e) {
				$.ajax({
					type: 'delete',
					url: url,
					data: data,
					success: function(msg){
						//success, reload the view for flash message
				    	location.reload();
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						//replace this with something more user friendly in production
						console.log(textStatus+': '+errorThrown);
						console.log(XMLHttpRequest);
					}
				});
			})
		});
	</script>

@endsection