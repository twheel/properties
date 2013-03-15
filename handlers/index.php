<?php

$p = new properties\Properties;

// Get the parameters for this request
list ($url_type, $property_title) = $tpl->controller->params;

// Verify the $type and get the title
list ($type, $page->title) = Property::url_to_type ($url_type);

// Build the query
$parameters = array ();
$ft_parameters = array ();

if ($type != 'All') {
	$parameters['type'] = $type;
}

if ($property_title != '') {
	$parameters['id'] = $property_title;
}

if (isset ($_GET['q']) && $_GET['q'] != '') {
	$page->title = 'Search Results';
	$qsane = Template::sanitize ('*'.trim($_GET['q']).'*');
	if (strtolower ($qsane) == 'homes' || strtolower ($qsane) == 'rentals') $qsane = substr ($qsane, 0, -1);
	$ft_parameters['properties'] = $qsane;
	$page->q = $_GET['q'];
}

// Get the properties
$properties = $p->prop_query ($parameters, $ft_parameters);

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

	// var_dump ($property);

	if (defined ('SAVING_AS_PDF')) {
		conf ('General', 'compress_output', false);
		$page->layout = 'pdf';
		echo $tpl->render (
			'properties/pdf',
			$property
		);
	} else {
		$property->url_type = $url_type;
		echo $tpl->render (
			'properties/property',
			$property
		);
	}

} else {

	if (count($properties)) $page->hide_sidebar = true;

	// category or all listings
	echo $tpl->render (
		'properties/index',
		array (
			'properties' => $properties,
			'url_type' => $tpl->controller->params[0]
		)
	);
}

?>
