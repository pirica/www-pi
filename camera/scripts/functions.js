
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
		$(this).src = '//' + $(this).data('address') + "/cam_pic.php?time=" + new Date().getTime();
	});
});

function reload_img (e) {
	$(e.target).src = '//' + $(this).data('address') + "/cam_pic.php?time=" + new Date().getTime();
}

function error_img (e) {
	setTimeout(function(){
		$(e.target).src = '//' + $(this).data('address') + "/cam_pic.php?time=" + new Date().getTime();
	}, 100);
}
