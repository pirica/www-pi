
<?php if($pages > 1){ ?>
	<ul class="pagination pagination-sm">
		<li gd-page="<?=$page-1?>" <?php if($page == 1){ ?>class="disabled"<?php } ?>><a href="?action=details&amp;id_grab=<?=$id_grab?>&amp;page=<?=$page-1?>&amp;perpage=<?=$perpage?>&amp;sort=<?=$sort?>&amp;sortorder=<?=$sortorder?>&amp;search=<?=$search?>">&laquo;</a></li>
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
					<li gd-page="<?=$i?>"><a href="?action=details&amp;id_grab=<?=$id_grab?>&amp;page=<?=$i?>&amp;perpage=<?=$perpage?>&amp;sort=<?=$sort?>&amp;sortorder=<?=$sortorder?>&amp;search=<?=$search?>"><?=$i?></a></li>
				<?php
				}
			}
		?>
		<?php if($show_last_dots){ ?><li <?php /*if($page == $pages)*/{ ?>class="disabled"<?php } ?>><a href="#">&#8230;</a></li><?php } ?>
		<li gd-page="<?=$page+1?>" <?php if($page == $pages){ ?>class="disabled"<?php } ?>><a href="?action=details&amp;id_grab=<?=$id_grab?>&amp;page=<?=$page+1?>&amp;perpage=<?=$perpage?>&amp;sort=<?=$sort?>&amp;sortorder=<?=$sortorder?>&amp;search=<?=$search?>">&raquo;</a></li>
	</ul>
<?php } ?>
