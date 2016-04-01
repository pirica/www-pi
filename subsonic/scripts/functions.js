
$().ready(function(){
	
	$('#filter_all').change(function(){
		$(this).parents('form').submit();
	});
	
	$('#allsongs').change(function(){
		$('input[name=song]').prop( "checked", $('#allsongs').is(':checked') );
		onSongsChecked();
	});
	
	$('input[name=song]').change(function(){
		onSongsChecked();
	});
	
	$('#myModal').on('click', '.add-playlist-entry', addPlaylistEntries);
	
});

function onSongsChecked()
{
	var allChecked = true;
	var songIds = '';
	
	$('input[name=song]').each(function(el){
		if($(this).is(':checked')){
			songIds += (songIds == '' ? '' : ',') + $(this).val();
		}
		else {
			allChecked = false;
		}
	});
	
	// tick the 'check all' box if everything is selected
	$('#allsongs').prop( "checked", allChecked);
	
	// get all selected song ids and add to 'all'-buttons
	$('#btnAddAll').attr('href', 'index.php?action=add_playlist_entry&songId=' + songIds);
	$('#btnRemoveAll').attr('href', 'index.php?action=delete_playlist_entry&songId=' + songIds);
	
}

function addPlaylistEntries(event)
{
	event.stopPropagation();
	event.preventDefault();
	
	$.ajax({
		url: $(event.target).attr('href'),
		type: 'GET',
		cache: false,
		dataType: 'json',
		error: function(xhr, status, error) {
			//location.href = ...
		},
		success: function(data, textStatus, jqXHR){
			location.reload();
		}
	});
}
