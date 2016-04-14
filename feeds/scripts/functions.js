

$().ready(function(){
	
    $('.panel-heading').data('entriesloaded', false);
	
	$('.panel-heading').click(function(){
		$('.panel-body').hide(500);
		
		if($(this).data('entriesloaded') == false)
		{
			$(this).parents('.panel').find('.panel-body').show(500);
			// ajax call to get items
            $(this).data('entriesloaded', true);
			$(this).parents('.panel').find('.list-group').load('?action=entries&id_feed=' + $(this).attr('id_feed') + '&ajaxcall=1&_=' + (new Date()).getTime(), initHandlers);
		}
		else
		{
			$(this).data('entriesloaded', false);
			$(this).parents('.panel').find('.list-group').html('');
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

function initHandlers(response, status, xhr){

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
		$(this).data('entriesloaded', false);
		countItems(this);
	});
	
	$('.btn-mark-all-as-read').each(function(){
		countItems(this);
	});
}

function countItems(which){
	var entries = parseInt($(which).parents('.panel-default').find('.list-group-item:visible').length);
	if(entries < 0)
	{
		entries = 0;
	}
	$(which).parents('.panel-default').find('.title-entries').html('(' + entries + ')');
	
	if(entries == 0)
	{
		//$(this).parents('.panel').hide();
	}
}
