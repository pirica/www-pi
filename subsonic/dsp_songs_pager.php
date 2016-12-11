
<?=($bottom_pager ? '<a name="bottom"></a>':'')?>

<?php
if($pages > 1)
{
	$pager_url = '';
	$pager_url .= '?action=' . $action->getCode();
	//$pager_url .= '&amp;page='. ($page-1);
	$pager_url .= '&amp;perpage='.$perpage;
	$pager_url .= '&amp;sort='.$sort;
	$pager_url .= '&amp;sortorder='.$sortorder;
	$pager_url .= '&amp;search='.$search;
	$pager_url .= '&amp;playlist='.$playlist;
	$pager_url .= '&amp;playlistId='.$playlistId;
	$pager_url .= '&amp;genreId='.$genreId;
	$pager_url .= '&amp;mainGenreId='.$mainGenreId;
	
	$pager_url_a = $bottom_pager ? '#bottom' : '';
	
	?>
	<ul class="pagination pagination-sm">
		<li gd-page="<?=$page-1?>" <?php if($page == 1){ ?>class="disabled"<?php } ?>><a href="<?= $pager_url . '&amp;page='. ($page-1) . $pager_url_a ?>">&laquo;</a></li>
		<?php if($show_first_dots){ ?><li <?php /*if($page == 1)*/{ ?>class="disabled"<?php } ?>><a href="#">&#8230;</a></li><?php } ?>
		<?php 
			for($i = $pages_start; $i <= $pages_end; $i++){
				if($i == $page){
				?>
					<li class="active"><span><?=$i?> <span class="sr-only">(current)</span></span></li>
				<?php
				}
				else {
				?>
					<li gd-page="<?=$i?>"><a href="<?= $pager_url . '&amp;page='. $i . $pager_url_a ?>"><?=$i?></a></li>
				<?php
				}
			}
		?>
		<?php if($show_last_dots){ ?><li <?php /*if($page == $pages)*/{ ?>class="disabled"<?php } ?>><a href="#">&#8230;</a></li><?php } ?>
		<li gd-page="<?=$page+1?>" <?php if($page == $pages){ ?>class="disabled"<?php } ?>><a href="<?= $pager_url . '&amp;page='. ($page+1) . $pager_url_a ?>">&raquo;</a></li>
	</ul>
<?php
}
?>
