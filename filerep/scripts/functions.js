
$().ready(function(){
	
	$('#filter_all').change(function(){
		$(this).parents('form').submit();
	});
	
	$('.act-dir-reindex').click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		setDirReindexing(this);
	});
	
	
	var settings = {
		url: "YOUR_MULTIPE_FILE_UPLOAD_URL",
		method: "POST",
		allowedTypes:"jpg,png,gif,doc,pdf,zip",
		fileName: "myfile",
		multiple: true,
		onSuccess:function(files,data,xhr)
		{
			alert("Upload success");
		},
		onError: function(files,status,errMsg)
		{       
			alert("Upload Failed");
		}
	}
	 
	$("#mulitplefileuploader").uploadFile(settings);
});


function setDirReindexing(el, val){
	var 
		_dir = $(el).data('dir')
	;
	$.ajax({
		url: 'index.php?action=do_set_directory_reindex' + 
				'&id_share=' + id_share + 
				'&dir=' + _dir + 
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
	
	$(el).replaceWith('<span class="fa fa-bolt red" title="Indexing..."></span>');
}