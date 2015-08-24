<div class="col-sm-12">	
	<div class="row">
		<div class="col-sm-8">
			<div class="form-group">
				{!! Form::label('title', 'Trip Title:') !!}
				{!! Form::text('title', null, ['class' => 'form-control']) !!}
			</div>
		</div>
		<div class="col-sm-4">
			<div class="col-sm-12">
				{!! Form::label('type', 'Select the Type of Trip:') !!}
				<div class="form-group text-center">
					<div id="typeButtons" class="btn-group" data-toggle="buttons">
						<label id="landButton" class="btn btn-lg btn-default">
							{!! Form::radio('type','land',(Input::get('type')=="land")) !!} Land
						</label>
						<label id="airButton" class="btn btn-lg btn-default">
							{!! Form::radio('type','air',(Input::get('type')=="air")) !!} Air
						</label>
						<label id="seaButton" class="btn btn-lg btn-default">
							{!! Form::radio('type','sea',(Input::get('type')=="sea")) !!} Sea
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-4">
		    <div class="form-group">
		    	{!! Form::label('start_time', 'Start Time:') !!}
		        <div class="input-group date">
		            <span class="input-group-addon">
		                <i class="fa fa-fw fa-lg fa-calendar-o"></i>
		            </span>
		            {!! Form::text('start_time', null, ['class' => 'form-control', 'placeholder' => 'Select a Date and Time']) !!}
		        </div>
		    </div>
		</div>
		<div class="col-sm-4">
			<div class="form-group">
		        {!! Form::label('end_time', 'End Time:') !!}
		        <div class="input-group date">
		            <span class="input-group-addon">
		                <i class="fa fa-fw fa-lg fa-calendar-times-o"></i>
		            </span>
		            {!! Form::text('end_time', null, ['class' => 'form-control', 'placeholder' => 'Select a Date and Time']) !!}
		        </div>
		    </div>
		</div>
		{!! Form::hidden('offset', null, ['id' => 'offset']) !!}
		<div class="col-sm-4">
			<div class="form-group">
				{!! Form::label('alert_timer', 'Notify Emergency Contacts After:') !!}
		        <div class="input-group date">
		            <span class="input-group-addon">
		                <span class="fa fa-fw fa-lg fa-clock-o"></span>
		            </span>
		            {!! Form::text('alert_timer', null, ['class' => 'form-control', 'placeholder' => 'Select an Amount of Time']) !!}
		        </div>
		    </div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="form-group">
				{!! Form::label('decription', 'Trip Description:') !!}
				{!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '5']) !!}
			</div>
		</div>
	</div>
</div>

@section('scripts')
	<!-- moment.js -->
	<script src="/js/vendor/moment.min.js"></script>
	<!-- datetimepicker -->
	<script src="/js/vendor/bootstrap-datetimepicker.min.js"></script>
	<!-- datetimepicker implementation -->
	<script type="text/javascript">
        $(function () {
        	//capture existing value if editing
        	var start = moment($('#start_time').val()).format("YYYY/MM/DD h:mm A");
        	var end = moment($('#end_time').val()).format("YYYY/MM/DD h:mm A");

        	//options for start_time
            $('#start_time').datetimepicker({
            	format: "YYYY/MM/DD h:mm A",
            	stepping: 5,
            	useCurrent: false,
            	sideBySide: true
            });
            //set value if editing
            $('#start_time').data("DateTimePicker").date(start);
            
            //options for end_time
            $('#end_time').datetimepicker({
            	format: "YYYY/MM/DD h:mm A",
            	stepping: 5,
            	useCurrent: false,
            	sideBySide: true
            });
            //set value if editing
            $('#end_time').data("DateTimePicker").date(end);

            //prevent end date preceeding start date and vice versa
            $("#start_time").on("dp.change", function (e) {
            	$('#end_time').data("DateTimePicker").minDate(e.date);
	        });
	        $("#end_time").on("dp.change", function (e) {
	            $('#start_time').data("DateTimePicker").maxDate(e.date);
	        });

            //options for alert_timer
            $('#alert_timer').datetimepicker({
            	format: 'HH:mm',
            	stepping: 15,
            	useCurrent: false
            });

            //set up offset field
            $('#offset').datetimepicker({
            	format: 'Z'
            });
            //set user's UTC offset
            $("#start_time, #end_time").on("dp.change", function (e) {
            	$('#offset').data("DateTimePicker").defaultDate(moment());
            });
            
            //set alert_timer to trip type defaults
            $('input:radio[name="type"]').change(function() {
				if (this.checked && this.value == 'land') {
				    $('#alert_timer').data("DateTimePicker").date('3:00');
				}
				else if (this.checked && this.value == 'air') {
					$('#alert_timer').data("DateTimePicker").date('1:00');
				}
				else if (this.checked && this.value == 'sea') {
					$('#alert_timer').data("DateTimePicker").date('6:00');
				}
			});

			//reselect trip type if form has validation error
			var selectedType = $('input[type=radio][name="type"]:checked').val();
			if (selectedType == 'land') {
				$('#landButton').addClass('active');
			}
			else if (selectedType == 'air') {
				$('#airButton').addClass('active');
			}
			else if (selectedType == 'sea') {
				$('#seaButton').addClass('active');
			}
        });
    </script>
@stop