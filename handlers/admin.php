<?php

$this->require_admin ();

$sort = 'name';
$allowed_sorts = array ('name', 'type', 'title');
if (isset ($_GET["sort"]) && in_array ($_GET["sort"], $allowed_sorts)) {
	$sort = $_GET["sort"];
}

$desc = '';
$allowed_desc = array ('name', 'type', 'title');
if (isset ($_GET["desc"]) && in_array ($_GET["desc"], $allowed_desc) && $_GET["desc"] == $sort) {
	$desc = $_GET["desc"];
}

// Build the query
$parameters = array ();
$ft_parameters = array ();
$wheres = array ();
$orders = array ();

if (isset ($_GET['q']) && $_GET['q'] != '') {
	$page->q = $_GET['q'];
	$page->qs = 'q='.$page->q.'&';
	$ft_parameters = Property::get_search_parameters ($_GET['q']);
}

$page->layout = 'admin';
$page->title = __ ('Properties');

// Calculate the offset
$limit = 20;
$num = isset ($this->params[0]) ? $this->params[0] : 1;
$offset = ($num - 1) * $limit;

// Get the properties
$p = properties\Properties::prop_query ($parameters, $ft_parameters, $wheres, $orders);

// Fetch the items and total items
if ($sort != $desc) {
	$p->order ($sort);
} else {
	$p->order ($sort, 'DESC');
}
if ($sort != 'name') $p->order ('name'); // so that if sorted by type, results are alphabetical within type
$items = $p->fetch ($limit, $offset);
$total = $p->count ();

// info (DB::$last_sql);
// info (DB::$last_args);

// exit;

// Pass our data to the view template
echo $tpl->render (
	'properties/admin',
	array (
		'limit' => $limit,
		'total' => $total,
		'items' => $items,
		'desc' => $desc,
		'sort' => $sort,
		'qs' => $page->qs,
		'count' => count ($items),
		'url' => '/properties/admin/%d'
	)
);

?>
