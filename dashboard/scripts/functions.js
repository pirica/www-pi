
var
	cam_image
	//cam_address
;

$().ready(function(){
	// raspimjpeg
	if($('#raspimjpeg_dest').length > 0){
		cam_image = document.getElementById("raspimjpeg_dest");
		cam_image.onload = reload_img;
		cam_image.onerror = error_img;
		reload_img();
	}
});

function reload_img () {
  cam_image.src = '//' + cam_address + "/cam_pic.php?time=" + new Date().getTime();
}

function error_img () {
  setTimeout("reload_img();", 100);
}
