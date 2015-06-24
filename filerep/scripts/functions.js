
$().ready(function(){
	
	$('#filter_all').change(function(){
		$(this).parents('form').submit();
	});
	
	$('.act-dir-reindex').click(function() {
		setDirReindexing(this);
	});

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
			$(el).replaceWith('<span class="fa fa-bolt red" title="Indexing..."></span>');
		}
	});
}