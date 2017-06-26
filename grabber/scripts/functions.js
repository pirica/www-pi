
var
	filesGrid = false,
	sortHovered = false,
	sortShifted = false,
	currentHover = ''
;

$().ready(function(){
	
	filesGrid = $('#files-grid').length > 0;
	
	//var hash = getHashValues(); 
	//setHashValues(hash);
	
	
	$('input[name="daterange"]').daterangepicker();
	
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
	
	
	initDetailGrid();
	
	// keydown/up actions for sorting while holding down shift: reverse sortorder
	$(window).keydown(function(e){
		if(e.shiftKey){
			sortShifted = true;
		}
		showSortIcon();
	});
	$(window).keyup(function(e){
		sortShifted = false;
		showSortIcon();
	});
	
	
	// searchfield filtering
	$('#files-form #search').change(function(){
		search = $('#files-form #search').val();
		reloadDetailGrid();
	});
	
	// filtering on status
	$('#files-form #status').change(function(){
		status = $('#files-form #status').val();
		reloadDetailGrid();
	});
	
	
	// enter filename when download url is changed
	$('#frm-addfile #grab_url').change(function(){
		var u = $('#frm-addfile #grab_url').val();
		
		var f = u.split('/');
		f = f[f.length - 1];
		
		$('#frm-addfile #grab_filename').val(fixFileName(f));
		
		//if(u.toLowerCase().indexOf("youtube.com") > 0){
		if(u != ''){
			$('#grab_filename').parent().find('.fa-spin').removeClass('hidden');
			$.ajax({
				url: 'index.php?action=js_check_url' + 
						'&u=' + u + 
					'',
				type: 'GET',
				cache: false,
				dataType: 'json',
				error: function(xhr, status, error) {
					//location.href = ...
					$('#grab_filename').parent().find('.fa-spin').addClass('hidden');
				},
				success: function(data, textStatus, jqXHR){
					if(typeof data.filename !== undefined){
						var f = (''+data.filename.replace(/\r|\n|\t/g, ''));
						if(f.replace(/ /g, '') != ''){
							$('#frm-addfile #grab_filename').val(fixFileName(f));
						}
					}
					if(typeof data.type !== undefined){
						$('#grab_type').find('option').each(function(){
							if($(this).val() == data.type){
								$(this).prop('selected', 'selected');
							}
						});
					}
					$('#grab_filename').parent().find('.fa-spin').addClass('hidden');
				}
			});
		}
	});
	
	// enter filename when download url is changed
	$('#frm-addfile #grab_path, #frm-addfile #grab_filename').change(function(){
		$('#frm-addfile #grab_filename').val(fixFileName($('#frm-addfile #grab_filename').val()));
		// check filename if exists
		if($('#frm-addfile #grab_filename').val() != ''){
			$.ajax({
				url: 'index.php?action=js_check_file' + 
						'&file=' + $('#frm-addfile #grab_path').val() + $('#frm-addfile #grab_filename').val() + 
					'',
				type: 'GET',
				cache: false,
				dataType: 'text',
				error: function(xhr, status, error) {
					//location.href = ...
				},
				success: function(data, textStatus, jqXHR){
					var d = (''+data.replace(/\r|\n|\t| /g, '')).toLowerCase();
					if(d == '1' || d == 'true'){
						$('.alert-file-exists').removeClass('hidden');
					}
					else {
						$('.alert-file-exists').addClass('hidden');
					}
				}
			});
		}
		else {
			$('.alert-file-exists').addClass('hidden');
		}
	});
	
	$('.queue_status, .queue_filename, .queue_directory, .queue_playlist').focusout(function() {
		updateQueueField(this, $(this).attr('name'), $(this).val());
	});
	
	$('.btn-queue-action').click(function(e){
		e.preventDefault();
		$(this).parents('.row').hide(200);
		$.ajax({
			url: $(this).attr('href'),
			type: 'GET',
			cache: false,
			dataType: 'json',
			error: function(xhr, status, error) {
				//location.href = ...
			},
			success: function(data, textStatus, jqXHR){
				
			}
		});
		return false;
	});
	
});

function fixFileName(val){
	val = val.replace(/\/|:|\*|\?|\"|\<|\>|\||\\|\~|\[|\]|\(|\)|\^|!|\=|\{|\}|\'/g, '-');
	val = val.replace(/\r|\n|\t|\+/g, ' ');
	
	val = val.replace(/#/g, '-hash-');
	val = val.replace(/%/g, '-pct-');
	val = val.replace(/\&/g, '-and-');
	val = val.replace(/@/g, '-at-');
	val = val.replace(/=/g, '-eq-');
	
	val = val.replace(/  /g, ' ');
	val = val.replace(/  /g, ' ');
	val = val.replace(/  /g, ' ');
	
	val = val.replace(/--/g, '-');
	val = val.replace(/--/g, '-');
	val = val.replace(/--/g, '-');
	
	return val;
}

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

function showSortIcon(){
	var
		current = '.thsort-' + sort + '-' + sortorder,
		currentOther  = '.thsort-' + currentHover + '-' //+ sortorder  //(sortorder == 'asc' ? 'desc' : 'asc')
	;
	
	if(sortShifted){
		currentOther += sortorder == 'asc' ? 'desc' : 'asc';
	}
	else {
		currentOther += sortorder;
	}
	
	if(sortHovered){
		$('.thsort').addClass('hidden');
		$(current).addClass('hidden');
		$(currentOther).removeClass('hidden');
		//$('.thsort-' + $(c).attr('sortfield') + '-' + (sortorder == 'asc' ? 'desc' : 'asc')).removeClass('hidden');
	}
}

function initDetailGrid(){
	if(filesGrid){
		
		// hover on table header -> sort
		$('.thsort').parent().hover(
			// handlerIn
			function(e){
				sortHovered = true;
				currentHover = $(this).attr('sortfield');
				showSortIcon();
			},
			//handlerOut
			function(e){
				sortHovered = false;
				currentHover = '';
				
				var
					current = '.thsort-' + sort + '-' + sortorder
					//currentOther  = '.thsort-' + $(this).attr('sortfield') + '-' //+ sortorder //(sortorder == 'asc' ? 'desc' : 'asc')
				;
				
				$('.thsort').addClass('hidden');
				$(current).removeClass('hidden');
				//$(currentOther).addClass('hidden');
				
			}
		).click(function(e){
			e.preventDefault();
			e.stopPropagation();
			sort = currentHover;
			if(sortShifted){
				sortorder = sortorder == 'asc' ? 'desc' : 'asc';
			}
			
			reloadDetailGrid();
		});
		
		
		// page nbrs, intercept and do ajax call instead 
		$('.pagination li').click(function(e){
			e.preventDefault();
			e.stopPropagation();
			var oldPage = page;
			if($(this).hasAttr('gd-page')){
				page = parseInt($(this).attr('gd-page'));
				if(page > 0){
					reloadDetailGrid();
				}
				else {
					page = oldPage;
				}
			}
		});
	
	}	
}

function reloadDetailGrid(){
	if(filesGrid){
		$.ajax({
			url: 'index.php?action=detailsgrid' + 
					'&id_grab=' + id_grab + 
					'&sort=' + sort + 
					'&sortorder=' + sortorder +
					'&page=' + page +
					'&perpage=' + perpage +
					'&status=' + status +
					'&search=' + search +
				'',
			type: 'GET',
			cache: false,
			//dataType: 'jsonp',
			error: function(xhr, status, error) {
				//location.href = ...
			},
			success: function(data, textStatus, jqXHR){
				$('#files-grid').html(data);
				initDetailGrid();
			}
		});
		/*
		location.href = 'index.php?action=details' + 
					'&id_grab=' + id_grab + 
					'&sort=' + sort + 
					'&sortorder=' + sortorder +
					'&page=' + page +
					'&perpage=' + perpage +
					'&status=' + status +
					'&search=' + search +
				'';
		*/
	}
}
/*
function getHashValues(){
	var
		ret = {}, i,
		u = document.URL,
		h = u.substr(u.indexOf('#') + 1),
		uas, ua = u.split('|')
	;
	for(i=0; i<ua.length; i++){
		uas = ua[i].split('=');
		ret[uas[0]] = uas[1];
	}
	
	return ret;
}

function setHashValues(hash){
	var v, h = '#';
	for each(v in hash){
		h += v + '=' + hash[v] + (h == '#' ? '' : '|');
	}
	location.hash = h;
}
*/


function updateQueueField(el, field, val){
	var 
		id_queue = $(el).data('id_queue')
	;
	$.ajax({
		url: 'index.php?action=do_edit_queue' + 
				'&id_queue=' + id_queue + 
				'&field=' + encodeURIComponent(field) + 
				'&value=' + encodeURIComponent(val) + 
			'',
		type: 'GET',
		cache: false,
		dataType: 'json',
		error: function(xhr, status, error) {
			//location.href = ...
		},
		success: function(data, textStatus, jqXHR){
			
		}
	});
}
