
var hostsGrid = false;

$().ready(function(){
	
	hostsGrid = $('#hosts-grid').length > 0;
	
    if(hostsGrid){
        setTimeout('reloadHostsOverview()', 10000);
    }
    
    $('.section').width();
	
	$('#filter_show, #filter_telemeter').change(function(){
		$(this).parents('form').submit();
	});
	
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