

$().ready(function(){
	
	$(".thumb-ctr img").on("error", function(){
		$(this).attr('src', 'thumb.php?src=' . $(this).attr('src').replace('thumbs/180prop/', ''));
	});
	
});
