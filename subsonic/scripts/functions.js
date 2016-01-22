
$().ready(function(){
	
	$('#filter_all').change(function(){
		$(this).parents('form').submit();
	});
	
	$('#allsongs').change(function(){
		$('input[name=song]').prop( "checked", $('#allsongs').is(':checked') );
	});
	
	$('input[name=song]').change(function(){
		var allChecked = true;
		$('input[name=song]').each(function(el){
			if(!$(this).is(':checked')){
				allChecked = false;
				break;
			}
		});
		$('#allsongs').prop( "checked", allChecked);
	});
	
});
