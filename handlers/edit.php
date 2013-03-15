<?php

$this->require_admin ();

$page->layout = 'admin';
$page->title = __ ('Edit Property');

$form = new Form ('post', $this);

/// $p = new properties\Properties;
/// $p->where ('id='.Template::Sanitize ($_GET['id']));
/// $property = $p->fetch();
/// $form->data = $property[0];
$form->data = new properties\Properties ($_GET['id']);

// info (DB::$last_sql);
// info (DB::$last_args);

echo $form->handle (function ($form) {
	$properties = $form->data;

	// Clean id and get directory to save files into
	$id = Property::clean_image_name ($_POST["id"]);
	$dir = '/files/properties/'.$id;
	$sroot = $_SERVER["DOCUMENT_ROOT"];

	// if $id has changed, rename folder
	if ($id != $properties->id) {
		$old_dir = '/files/properties/'.$properties->id;
		if (file_exists ($sroot.$old_dir)) {
			rename ($sroot.$old_dir, $sroot.$dir);
		}
	}

	// Make sure property folder exists
	$path = Property::mk_prop_dir ($dir);

	// Move main image to property folder
	$main_image = Property::move_file ($_POST['main_image'], $dir, '1');
	if (file_exists ($main_image)) Image::Resize ($main_image, 600, 400, 'contain');
	$desired_files[] = $main_image;

	// Move images to property folder and resize them
	$images = Property::move_multiple ($_POST['images'], $dir);
	foreach ($images as $image) {
		if (file_exists ($image)) Image::Resize ($image, 600, 400, 'contain');
	}
	$desired_files = array_merge ($desired_files, $images);
	$images = implode('|', $images);

	// Move documents to property folder
	$documents = Property::move_multiple ($_POST['documents'], $dir);
	$desired_files = array_merge ($desired_files, $documents);
	$documents = implode('|', $documents);

	$existing_files = str_replace($sroot, '', glob($sroot.$dir.'/*'));
	foreach (array_diff ($existing_files, $desired_files) as $remove_file) {
		unlink ($sroot.$remove_file);
		FileManager::prop_delete (preg_replace('/^\/files\//', '', $remove_file));
	}

	// Update the properties 
	$properties->id = $_POST['id'];
	$properties->name = $_POST['name'];
	$properties->type = $_POST['type'];
	$properties->title = $_POST['title'];
	$properties->MLS = $_POST['MLS'];
	$properties->main_image = $main_image;
	$properties->images = $images;
	$properties->documents = $documents;
	$properties->teaser = $_POST['teaser'];
	$properties->description = $_POST['description'];
	$properties->price = $_POST['price'];
	$properties->status = $_POST['status'];
	$properties->address = $_POST['address'];
	$properties->directions = $_POST['directions'];
	$properties->map = $_POST['map'];
	$properties->on_hold = $_POST['on_hold'];
	$properties->put ();

	// echo "\n\n";
	// var_dump ($properties);

	// info (DB::$last_sql);
	// var_dump (DB::$last_args);
	// exit;

	if ($properties->error) {
		// Failed to save
		$form->controller->add_notification (__ ('Unable to save properties.'));
		info ($properties->error);
		info (DB::$last_sql);
		info (DB::$last_args);
		return;
	}

	// Save a version of the properties 
	Versions::add ($properties);

	// Notify the user and redirect on success
	$form->controller->add_notification (__ ('Property saved.'));
	$form->controller->redirect ('/properties/admin');
});

?>
