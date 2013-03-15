<?php

$this->require_admin ();

$page->layout = 'admin';
$page->title = __ ('Add Property');

$form = new Form ('post', $this);

echo $form->handle (function ($form) {
	// Clean id
	$id = Property::clean_image_name ($_POST["id"]);

	// Make sure property folder exists
	$dir = '/files/properties/'.$id;
	$path = Property::mk_prop_dir ($dir);

	// Move main image to property folder
	$main_image = Property::move_file ($_POST['main_image'], $dir, '1');
	if (file_exists ($main_image)) Image::Resize ($main_image, 600, 400);

	// Move images to property folder and resize them
	$images = Property::move_multiple ($_POST['images'], $dir);
	foreach ($images as $image) {
		if (file_exists ($image)) Image::Resize ($image, 600, 400, 'contain');
	}
	$images = implode('|', $images);

	// Move documents to property folder
	$documents = Property::move_multiple ($_POST['documents'], $dir);
	$documents = implode('|', $documents);

	// Create and save a new property 
	$properties = new properties\Properties (array (
		'id' => $_POST['id'], 
		'name' => $_POST['name'], 
		'type' => $_POST['type'], 
		'title' => $_POST['title'], 
		'MLS' => $_POST['MLS'], 
		'main_image' => $main_image, 
		'images' => $images, 
		'documents' => $documents, 
		'teaser' => $_POST['teaser'], 
		'description' => $_POST['description'], 
		'price' => $_POST['price'], 
		'status' => $_POST['status'], 
		'address' => $_POST['address'], 
		'directions' => $_POST['directions'], 
		'map' => $_POST['map'], 
		'on_hold' => $_POST['on_hold'] 
	));
	$properties->put ();
	// info ($properties->error);
	// info (DB::$last_sql);
	// info (DB::$last_args);
	// return;

	if ($properties->error) {
		// Failed to save
		$form->controller->add_notification (__ ('Unable to save properties.'));
		return false;
	}

	// Save a version of the properties 
	Versions::add ($properties);

	// Notify the user and redirect on success
	$form->controller->add_notification (__ ('Property added.'));
	$form->controller->redirect ('/properties/admin');
});

?>
