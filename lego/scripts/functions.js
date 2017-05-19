

$().ready(function(){
	
	$(".thumb-ctr img").on("error", function(){
		if($(this).attr('src').indexOf('thumbs/180prop/') > -1)
		{
			$(this).attr('src', 'thumb.php?src=' + $(this).attr('src').replace('thumbs/180prop/', ''));
		}
	});
	
});
