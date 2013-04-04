<?php

// Get the parameters for this request
list ($url_type, $property_title) = $tpl->controller->params;

// Verify the $type and get the title
list ($type, $page->title) = Property::url_to_type ($url_type);

// Build the query
$parameters = array ();
$ft_parameters = array ();
$wheres = array ("on_hold = '0'");
$orders = array ('status', 'random()');

if ($type != 'All') {
	$parameters['type'] = $type;
}

if ($property_title != '') {
	$parameters['id'] = $property_title;
}

if (isset ($_GET['q']) && $_GET['q'] != '') {
	$page->q = $_GET['q'];
	$page->title = 'Search Results';
	$ft_parameters = Property::get_search_parameters ($_GET['q']);
}

// Get the properties
$p = properties\Properties::prop_query ($parameters, $ft_parameters, $wheres, $orders);
$properties = $p->fetch ();

// info (DB::$last_sql);
// info (DB::$last_args);

// redirect to individual page if only one found
if (count ($properties) == 1) {

	if (User::require_admin ()) $page->add_script ('/apps/filemanager/js/jquery.filemanager.js');

	$property = $properties[0]->orig();

	// title
	$page->title = $property->title;

	// map
	if ($property->map) {
		$page->sidebar = "<h3>Map</h3>\n<div id=\"map\">\n".$property->map."\n</div>\n";
	} elseif ($property->address) {
		$page->sidebar = '<iframe width="300" height="450" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?oe=utf-8&amp;q='.urlencode($property->address).'&amp;ie=UTF8&amp;gl=us&amp;t=m&amp;z=14&amp;iwloc=A&amp;output=embed"></iframe><br /><small><a href="http://maps.google.com/maps?oe=utf-8&amp;q='.urlencode($property->address).'&amp;ie=UTF8&amp;gl=us&amp;t=m&amp;z=14&amp;iwloc=A&amp;source=embed" style="color:#0000FF;text-align:left">View Larger Map</a></small>';
	} else {
		$page->hide_sidebar = true;
	}

	// documents
	if ($property->documents) {
		$documents = explode ('|', $property->documents);
		if (count($documents)) {
			$property->show_documents = true;
			$property->documents = array ();
			foreach ($documents as $document) {
				$property->documents[$document] = FileManager::prop (preg_replace ('/^\/files\//', '', $document), 'desc');
			}
		}
	}

	// directions
	if ($property->directions == '<p><br></p>') $property->directions = '';

	// var_dump ($property);

	if (defined ('SAVING_AS_PDF')) {
		conf ('General', 'compress_output', false);
		$page->layout = 'pdf';
		echo $tpl->render (
			'properties/pdf',
			$property
		);
	} else {

	// show admin edit buttons
		if (User::is_valid () && User::is ('admin')) {
			$lock = new Lock ('Property', $id);
			$property->locked = $lock->exists ();
			echo $tpl->render ('properties/editable', $property);
		}

		$property->url_type = $url_type;
		echo $tpl->render (
			'properties/property',
			$property
		);
	}

} else {

	if (count($properties)) $page->hide_sidebar = true;
	else $page->title = "";

	// category or all listings
	echo $tpl->render (
		'properties/index',
		array (
			'properties' => $properties,
			'url_type' => $tpl->controller->params[0],
			'id' => $property_title
		)
	);
}

?>
