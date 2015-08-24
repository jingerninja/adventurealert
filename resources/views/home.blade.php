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
					<h1>Welcome to AdventureAlert</h1>
				</div>
				<div class="panel-body">
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eu augue ut orci vestibulum fringilla. Mauris sed ornare sapien. Morbi commodo mattis massa in semper. Proin vitae ex sed orci rhoncus mollis. Maecenas sit amet elementum orci. Vivamus eleifend quis nisl et pulvinar. In pulvinar dolor a odio tristique, ac commodo odio tristique. Cras dignissim pellentesque neque, ac euismod dolor cursus ut. Suspendisse a vestibulum ante. Sed et semper massa. Aenean convallis pretium augue, eget sodales elit faucibus non.</p>
					<p>Pellentesque condimentum nibh in leo faucibus sodales. Pellentesque mollis magna a erat ultricies, eu ultrices erat eleifend. Nullam dui ex, consectetur vel ultricies iaculis, ornare et risus. Morbi quis purus vitae enim porttitor vestibulum nec non mi. Suspendisse dignissim viverra tincidunt. Suspendisse potenti. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Donec in scelerisque sem. Phasellus vel augue nibh. Curabitur vitae elementum enim.</p>
					<p>Donec consectetur sem elit, a fermentum tortor placerat porta. Vestibulum et convallis sem. Donec diam nulla, cursus eu mi vehicula, bibendum tincidunt nunc. Nam scelerisque lorem porta tellus tincidunt, et accumsan leo suscipit. Proin bibendum libero lacus, nec lobortis lacus viverra sed. Pellentesque ut ligula rutrum enim varius mattis. Donec elementum vel ipsum et posuere.</p>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="well text-center">
				<a href="/auth/register">Register Call to Action</a>
			</div>
			<div class="well">
				<p>Side bar item</p>
			</div>
			<div class="well">
				<p>Side bar item</p>
			</div>
		</div>
	</div>
</div>
@endsection
