

$().ready(function(){
	
	$('.panel-heading').click(function(){
		$('.panel-body').hide(500);
		//if($(this).parents('.panel').find('.panel-body:visible').length > 0){
			//$(this).parents('.panel').find('.panel-body').hide(500);
		//}
		//else {
			$(this).parents('.panel').find('.panel-body').show(500);
		//}
		
		if($(this).attr('entriesloaded') == 'false'){
			// ajax call to get items
            $(this).attr('entriesloaded', 'true');
			$(this).parents('.panel').find('.list-group').load('?action=entries&id_feed=' + $(this).attr('id_feed') + '&ajaxcall=1&_=' + (new Date()).getTime(), initHandlers);
		}
	});
	
	$('#feed_refresh').keyup(function(e){
		switch(e.keyCode){
			//case Keyboard.UP:
			case 38:
				$('#feed_refresh').val(parseInt($('#feed_refresh').val())+10);
				break;
			//case Keyboard.DOWN:
			case 40:
				$('#feed_refresh').val(parseInt($('#feed_refresh').val())-10);
				break;
			//case Keyboard.LEFT:
			case 37:
				
				break;
			//case Keyboard.RIGHT:
			case 39:
				
				break;
			//case Keyboard.SHIFT:
			case 16:
				
				break;
		}
		
		$('#feed_refresh_lbl').html( minutesToTimeRange($('#feed_refresh').val()) );
	});
	
	//initHandlers();
	
});

function initHandlers(){

	$('.btn-mark-as-read').click(function(e){
		e.preventDefault();
		e.stopPropagation();
		
		$.get($(this).attr('href'));
		$(this).parents('.list-group-item').hide(200);
		
		countItems(this);
	});
	
	$('.btn-mark-as-read-until').click(function(e){
		e.preventDefault();
		e.stopPropagation();
		
		$.get($(this).attr('href'));
		$(this).parents('.list-group-item').hide(200);
		$(this).parents('.list-group-item').prevAll().hide(200);
		
		countItems(this);
	});
	
	$('.btn-mark-all-as-read').click(function(e){
		e.preventDefault();
		e.stopPropagation();
		
		$.get($(this).attr('href'));
		
		$(this).parents('.panel').find('.panel-body').hide(500);
		$(this).attr('entriesloaded', 'false');
		countItems(this);
	});
	
}

function countItems(which){
	var entries = parseInt($(which).parents('.panel-default').find('.list-group-item:visible').length);
	$(which).parents('.panel-default').find('.title-entries').html('(' + entries + ')');
}
