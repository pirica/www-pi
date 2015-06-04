
var hostsGrid = false,
	hostsConfigGrid = false;

$().ready(function(){
	
	hostsGrid = $('#hosts-grid').length > 0;
	hostsConfigGrid = $('#hosts-config-grid').length > 0;
	
	if(hostsGrid){
		setTimeout('reloadHostsOverview()', 10000);
	}
	
	$('.section').width();
	
	$('#filter_show, #filter_telemeter, #filter_all').change(function(){
		$(this).parents('form').submit();
	});
	
	if(hostsConfigGrid){
		$('#hosts-config-grid input[type=text]').focusout(function() {
			updateHostValue(this, $(this).val());
		});
		$('#hosts-config-grid input[type=checkbox]').change(function() {
			updateHostValue(this, ($(this).is(":checked") ? 1 : 0));
		});
		$('#hosts-config-grid select').change(function() {
			updateHostValue(this, $(this).find('option:selected').val());
		});
		
	}
	
	// scroll to the currently selected host (id in url)
	if($("#host" + getUrlParameter('host')).length > 0){
		$('html, body').animate({
	        scrollTop: $("#host" + getUrlParameter('host')).offset().top - 100
	    }, 500);
	}
});


function reloadHostsOverview(){
	if(hostsGrid){
		$.ajax({
			url: 'index.php?action=jmain' + 
					//'&id_grab=' + id_grab + 
				'',
			type: 'GET',
			cache: false,
			dataType: 'json',
			error: function(xhr, status, error) {
				//location.href = ...
			},
			success: function(data, textStatus, jqXHR){
				var oldval;
				$('#date-last-update').html( data.date );
				for(i=0; i<data.data.length; i++){
					//for (col of data.data[i]){
					for (col in data.data[i]) {
						// find host's tr
						var c = $('#hosts-grid').find('.tr-host' + data.data[i].id_host + ' .' + col);
						// got it
						if($(c).length > 0){
							oldval = $(c).html();
							$(c).html( data.data[i][col] );
							if(oldval != data.data[i][col]){
								$(c).addClass('changed');
							}
							else {
								$(c).removeClass('changed');
							}
						}
						// not found, new host => add tr
						else {
							//$('#hosts-grid').
						}
					}
				}
				
			}
		});
		
		setTimeout('reloadHostsOverview()', 10000);
	}
}

function updateHostValue(el, val){
	var 
		id_host = $(el).parents('tr').data('id_host'),
		field = $(el).attr('name')
	;
	$.ajax({
		url: 'index.php?action=do_sethost' + 
				'&id_host=' + id_host + 
				'&field=' + field + 
				'&value=' + val + 
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


function getUrlParameter(sParam)
{
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) 
    {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) 
        {
            return (sParameterName.length > 1 ? sParameterName[1] : '');
        }
    }
}