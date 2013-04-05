<?php

$p = new properties\Properties;

parse_str ($_SERVER["QUERY_STRING"], $array);
var_dump ($_GET);
var_dump ($array);
exit;

// get the parameters for this request
list ($type, $property_title) = $tpl->controller->params;

if ($type == '') $type = 'All';

// verify the $type and get the title
list ($type, $page->title) = Property::url_to_type ($type);

$properties = array();
if ($type != 'All') {
	$properties = $p->type ($type);
} else {
	$properties = $p->all ();
}

// redirect to individual page if only one found
if (count ($properties) == 1) {
	$property_title = $properties[0]->data["title"];
}

// individual property
if ($property_title) {
	$property = $p->individual (urldecode ($property_title));
	$property = $property[0];
	$property = $property->orig();

	// title
	$page->title = $property->title;

	// map
	if ($property->map) $page->sidebar = "<h3>Map</h3>\n<div id=\"map\">\n".$property->map."\n</div>\n";

	// documents
	$show_documents = false;
	if ($property->documents) {
		$property->documents = explode ('|', $property->documents);
		if (count($properties->documents)) $show_documents = true;
	}

	echo $tpl->render (
		'properties/property',
		$property
	);

} else {

	// category or all listings
	echo $tpl->render (
		'properties/index',
		array (
			'properties' => $properties,
			'type' => $type,
			'url_type' => $tpl->controller->params[0]
		)
	);
}

?>
