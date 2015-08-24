@extends('app')

@section('content')

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-8 col-sm-offset-1">
			@if (Session::has('flash_message'))
				<div class="alert {{ session('alert_class') }}">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					{{ session('flash_message') }}
				</div>
			@endif
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="panel-title">Your Emergency Contacts</div>
				</div>
				<div class="panel-body">
					<div class="row">
						@foreach($e_contacts as $e_contact)
							<div class="col-sm-6">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<div class="row">
											<div class="col-sm-9 contact_name">{{$e_contact->name}}</div>
											<div class="pull-right" style="margin-right: 10px;">
												<div class="btn-group">
													<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
														<i class="fa fa-bars"></i>
													</button>
													<ul class="dropdown-menu context-menu">
														<li class="edit"><a href="/contacts/{{$e_contact->id}}/edit"><i class="fa fa-fw fa-pencil"></i></a></li>
														<li class="delete" ><a href="#" data-href="/contacts/{{$e_contact->id}}" data-toggle="modal" data-target="#confirm-delete" data-token="{{ csrf_token() }}"><i class="fa fa-fw fa-trash"></i></a></li>
													</ul>
												</div>
											</div>
										</div>
									</div>
									<div class="panel-body">
										<p class="contact_email"><i class="fa fa-envelope fa-lg fa-fw"></i> {{$e_contact->email}}</p>
										<p class="contact_phone"><i class="fa fa-phone fa-lg fa-fw"></i> {{phone_format($e_contact->phone, 'CA')}}</p>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="well text-center">
				<a href="/contacts/create">Create New Contact</a>
			</div>
		</div>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content alert-danger">
				<div class="modal-header alert-danger">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Are you sure you want to delete this contact?</h4>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-danger btn-ok">Delete</button>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')

	<script type="text/javascript">
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