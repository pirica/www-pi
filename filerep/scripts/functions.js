
$().ready(function(){
	
	$('#filter_all').change(function(){
		$(this).parents('form').submit();
	});
	
	$('.act-dir-reindex').click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		setDirReindexing(e, this);
	});
	
	if($("#mulitplefileuploader").length > 0){
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
	}
	
	if($("#freemulitplefileuploader").length > 0){
		var freesettings = {
			url: "?action=do_free_upload",
			method: "POST",
			//allowedTypes:"jpg,png,gif,doc,pdf,zip",
			fileName: "myfile",
			multiple: true,
			
			maxFileCount:max_file_uploads,
			maxFileSize:upload_max_filesize,
			
			onSuccess:function(files,data,xhr)
			{
				//alert("Upload success for: " + JSON.stringify(data));
			},
			onError: function(files,status,errMsg)
			{       
				//alert("Upload Failed");
			},
			afterUploadAll:function(obj)
			{
				alert("All files are uploaded");
				
			}
		}
		$("#freemulitplefileuploader").uploadFile(freesettings);
	}
	
	
	
	$('.filename').dblclick(function(event){
		$(this).find('span.rename_to').addClass('hidden');
		$(this).find('input').removeClass('hidden').focus();
		
	});
	
	$('.rename_to').blur(function(event){
		var oldfile = $(this).parent().find('span.orig').text();
		var newfile = $(this).val();
		
		$(this).parent().find('input').addClass('hidden');
		
		if(oldfile != newfile)
		{
			$(this).parent().find('span.rename_to').text(newfile).removeClass('hidden');
			
			if(newfile == '')
			{
				$(this).parent().find('.orig').removeClass('renamed');
			}
			else
			{
				$(this).parent().find('.orig').addClass('renamed');
			}
			
			var id_file = $(this).parents('tr').data('file');
			$.ajax({
				url: 'index.php?action=do_move_file' + 
						'&id_file=' + id_file + 
						'&id_share=' + id_share + 
						'&rename_to=' + encodeURIComponent(newfile) + 
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
		}
	});
	
});


function setDirReindexing(event, el){
	var 
		_dir = $(el).data('dir')
	;
	$.ajax({
		url: 'index.php?action=do_set_directory_reindex' + 
				'&id_share=' + id_share + 
				'&dir=' + _dir + 
				'&subs=' + (event.shiftKey ? '1' : '0') + 
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