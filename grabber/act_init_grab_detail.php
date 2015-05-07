<?php

$sort = strtolower(saneInput('sort'));
$sortorder = strtolower(saneInput('sortorder'));

$perpage = saneInput('perpage', 'int', -1);
$page = saneInput('page', 'int', 1);

$search = strtolower(saneInput('search'));
$status = strtoupper(saneInput('status'));

$max_pages = $settings->val('detailgrid_max_pages', 20);

// invalid values : reset to default
if(!in_array($sort, array('full_url', 'full_path', 'status', 'date_inserted', 'date_modified'))){
	$sort = $settings->val('detailgrid_default_sort', 'date_inserted');
}
if(!in_array($sortorder, array('asc', 'desc'))){
	$sortorder = $settings->val('detailgrid_default_sortorder', 'desc');
}
if($status == '' || !in_array($status, array('*', 'N', 'OK', 'FX', 'P', 'NF', 'TO', 'FE', 'E', 'X'))){
	$status = $settings->val('detailgrid_default_status', '*');
}

// invalid paging, reset
if($perpage <= 0){
	$perpage = $settings->val('detailgrid_items_perpage', 50);
}
if($page <= 0){
	$page = 1;
}

// get current page nbr
$pages = (int) floor(($grab_files_total + 0.0) / ($perpage + 0.0)) + 1;
$offset = ($page - 1) * $perpage;

// limit nbr of pages to 10 (pagination)
//$max_pages = $pages;
$morepages = 0;
$pages_start = 1;
$pages_end = $pages;
if($pages > $max_pages){
	$morepages = $pages - $max_pages;
	//$max_pages = 10;
	$pages_start = $page > ($max_pages / 2) ? $page - ($max_pages / 2) : 1;
	$pages_end = $pages_start + $max_pages - 1 > $pages ? $pages : $pages_start + $max_pages - 1;
}

// include dots left to page nbr 3 and/or right to page nbr $pages - 2
// if starting from page 3 (so if page 2 is shown, no dots - since this is just page 1)
$show_first_dots = false;
$show_last_dots = false;

// say for 20 pages, and 10 page nbrs visible, keep current page approximately in the middle
// so if page nbr > 5, show first dots
if($morepages > 0 && $page > $max_pages / 2 ){
	$show_first_dots = true;
}
// and if page nbr < 15, show last dots
if($morepages > 0 && $page < $pages - $max_pages / 2 ){
	$show_last_dots = true;
}

?>