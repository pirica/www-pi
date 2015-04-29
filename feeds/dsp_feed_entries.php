<?php
$entries = '';
while($entry = mysql_fetch_array($qry_feed_entries))
{
	$entries .= ($entries == '' ? '' : ',') . $entry['id_feed_entry'];
	?>
	<li class="list-group-item">
		<h4 class="list-group-item-heading"><?=$entry['entry_title']?> <span class="item-pubdate"><?=$entry['pubdate']?></span></h4>
		<p class="list-group-item-text">
			<a class="btn btn-danger btn-xs btn-mark-as-read" href="index.php?action=setfeedentry&id_feed_entry=<?=$entry['id_feed_entry']?>&is_read=1" data-entryid="<?=$entry['id_feed_entry']?>">Mark as read</a>
			<?=$entry['entry_description']?>
			<a href="<?=$entry['entry_link']?>" target="_blank">More...</a>
		</p>
	</li>
	<?php 
}
?>
<p>
	<a class="btn btn-danger btn-mark-all-as-read" href="index.php?action=setfeedentry&entries=<?= $entries ?>&is_read=1">All read</a>
</p>