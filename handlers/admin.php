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

$page->layout = 'admin';
$page->title = __ ('Properties');

// Calculate the offset
$limit = 20;
$num = isset ($this->params[0]) ? $this->params[0] : 1;
$offset = ($num - 1) * $limit;

// Fetch the items and total items
if ($sort != $desc) {
	$q = properties\Properties::query ()->order ($sort);
} else {
	$q = properties\Properties::query ()->order ($sort, 'DESC');
}
if ($sort != 'name') $q->order ('name'); // so that if sorted by type, results are alphabetical within type
$items = $q->fetch ($limit, $offset);
$total = $q->count ();

// Pass our data to the view template
echo $tpl->render (
	'properties/admin',
	array (
		'limit' => $limit,
		'total' => $total,
		'items' => $items,
		'desc' => $desc,
		'sort' => $sort,
		'count' => count ($items),
		'url' => '/properties/admin/%d'
	)
);

?>
