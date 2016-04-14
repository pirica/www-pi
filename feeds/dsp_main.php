
<h1>Overview</h1>
	
<?php

while($feed = mysql_fetch_array($qry_feed_overview))
{
	?>
		<div class="panel panel-default">
			<div class="panel-heading" id_feed="<?=$feed['id_feed']?>" entriesloaded="false">
				<h3 class="panel-title"><?=$feed['title']?> <span class="title-entries">(<?=$feed['entries']?>)</span></h3>
			</div>
			
			<div class="panel-body closed">
				<ul class="list-group">
					<!-- entries go here -->
				</ul>
				<p>
					<a class="btn btn-danger btn-mark-all-as-read" href="index.php?action=setfeedentry&entries=<?= $entries ?>&is_read=1">All read</a>
				</p>
			</div>
		</div>
	<?php
}
?>
