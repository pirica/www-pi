
/*
var
	cam_image
	//cam_address
;
*/

$().ready(function(){
	// raspimjpeg
	/*if($('#raspimjpeg_dest').length > 0){
		cam_image = document.getElementById("raspimjpeg_dest");
		cam_image.onload = reload_img;
		cam_image.onerror = error_img;
		reload_img();
	}*/
	$('.raspimjpeg').each(function(){
		$(this).on({
			load: reload_img, 
			error: error_img
		});
		var src = 'http://' + $(this).data('address') + "/cam_pic.php?time=" + (new Date().getTime());
		$(this).attr('src', src);
	});
	
	$('#camera').change(function(){
		$(this).parents('form').submit();
	});
});

function reload_img (e) {
	var src = 'http://' + $(e.target).data('address') + "/cam_pic.php?time=" + (new Date().getTime());
	$(e.target).attr('src', src);
}

function error_img (e) {
	setTimeout(function(){
		var src = 'http://' + $(e.target).data('address') + "/cam_pic.php?time=" + (new Date().getTime());
		$(e.target).attr('src', src);
	}, 100);
}
