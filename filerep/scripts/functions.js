
$().ready(function(){
	
	$('#filter_all').change(function(){
		$(this).parents('form').submit();
	});
	
	$('.act-dir-reindex').click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		setDirReindexing(this);
	});
	
	
	// http://hayageek.com/docs/jquery-upload-file.php
	var settings = {
		url: "?action=do_upload",
		method: "POST",
		//allowedTypes:"jpg,png,gif,doc,pdf,zip",
		fileName: "myfile",
		multiple: true,
		formData: {
			dir: dir,
			id_share: id_share,
			id_host: id_host
		},
		
		//sequential:true,
		//sequentialCount:1,
		
		maxFileCount:max_file_uploads,
		maxFileSize:upload_max_filesize,
		
		/*
		dragDropStr: "<span><b>Faites glisser et déposez les fichiers</b></span>",
		abortStr:"abandonner",
		cancelStr:"résilier",
		doneStr:"fait",
		multiDragErrorStr: "Plusieurs Drag &amp; Drop de fichiers ne sont pas autorisés.",
		extErrorStr:"n'est pas autorisé. Extensions autorisées:",
		sizeErrorStr:"n'est pas autorisé. Admis taille max:",
		uploadErrorStr:"Upload n'est pas autorisé",
		uploadStr:"Téléchargez",
		*/
		
		//onSubmit:function(files),
		//onCancel:function(files,pd),
		
		onSuccess:function(files,data,xhr)
		{
			alert("Upload success for: " + JSON.stringify(data));
		},
		onError: function(files,status,errMsg)
		{       
			alert("Upload Failed");
		},
		afterUploadAll:function(obj)
		{
			alert("All files are uploaded");
			
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