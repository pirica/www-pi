
$().ready(function(){
	/*
	$('.btn-delete-grab').click(function(){
		//$('#myModal').find('.modal-body').html( $.load($(this).attr('href')) );
		$('#myModal').modal({remote:$(this).attr('href') });
	)};
	*/
	
	$('#counter_type').change(onCounterTypeChange);
	onCounterTypeChange();
	
	/*
	$('#frm-edit-counter').submit(function(){
		var valid = true;
		
		return valid;
	});
	*/
	
	// check http://jqueryvalidation.org/documentation  
	$('#frm-edit').validate({
		rules: {
			grab_description: {
				required: true
			},
			grab_url: {
				required: true
			},
			grab_path: {
				required: true
			},
			grab_filename: {
				required: true
			},
			grab_max_grabbers: {
				required: false,
				digits: true,
				min: 1,
				max: 9999
			}
			
			/*
			,
			grab_excluded: {
				required: false
			},
			grab_run_between_from: {
				time: true
			},
			grab_run_between_to {
				time: true
			}
			
			*/
		},
		highlight: function(element) {
			$(element).closest('.form-group').removeClass('success').addClass('error');
		},
		success: function(element) {
			element
				.text('OK!').addClass('valid')
				.closest('.form-group').removeClass('error').addClass('success');
		}
	});
	
	// check http://jqueryvalidation.org/documentation  
	$('#frm-edit-counter').validate({
		rules: {
			counter_type: {
				required: true
			},
			counter_field: {
				required: true
			},
			
			counter_intfrom: {
				required: function(){
					$('#counter_type').val() == 'int'
				},
				digits: true
			},
			counter_intto: {
				required: function(){
					$('#counter_type').val() == 'int'
				},
				digits: true
			},
			
			counter_datefrom: {
				required: function(){
					$('#counter_type').val() == 'date'
				},
				dateISO: true
			},
			counter_dateto: {
				required: false, // empty for current date
				dateISO: true
			},
			
			counter_listvalues: {
				required: function(){
					$('#counter_type').val() == 'list'
				}
			}
		},
		highlight: function(element) {
			$(element).closest('.form-group').removeClass('success').addClass('error');
		},
		success: function(element) {
			element
				.text('OK!').addClass('valid')
				.closest('.form-group').removeClass('error').addClass('success');
		}
	});

});

function onCounterTypeChange(){
	$('.counter-fields').hide();
	switch($('#counter_type').val()){
		case 'date':
			$('.date-counter-fields').show();
			break;
		case 'int':
			$('.int-counter-fields').show();
			break;
		case 'list':
			$('.list-counter-fields').show();
			break;
	}
}
