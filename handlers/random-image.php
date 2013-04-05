<?php

/**
 * Photo gallery embed handler. Creates a gallery of the images
 * from the specified folder. Used by the WYSIWYG editor's dynamic
 * objects menu, or manually via:
 *
 *     {! properties/random-image?path=foldername !}
 *
 * The `foldername` is a folder of images inside `/files/` with optional subfolder (called `large`) of large images.
 */

if (User::require_admin ()) $page->add_script ('/apps/filemanager/js/jquery.filemanager.js');

require_once ('apps/filemanager/lib/Functions.php');

if (isset ($data['path'])) {
	$path = trim ($data['path'], '/');
} elseif (isset ($_GET['path'])) {
	$path = trim ($_GET['path'], '/');
} else {
	return;
}

if (strpos ($path, '..') !== false) {
	return;
}

if (! @is_dir ('files/' . $path)) {
	return;
}

// fetch the files
$files = glob ('files/' . $path . '/*.{jpg,jpeg,gif,png,JPG,JPEG,GIF,PNG}', GLOB_BRACE);

// get a random one
$file_path = $files[array_rand($files)];
$file_name = str_replace("files/$path/", '', $file_path);
$large = "files/$path/large/$file_name";
if (! (file_exists($large) && exif_imagetype($large))) $large = '';

// remove 'files/' from path
$pruned = preg_replace ('/^files\//', '', $file_path);

$template = 'properties/random-image';
$page->add_style ('/apps/filemanager/css/colorbox/colorbox.css');
$page->add_script ('/apps/filemanager/js/jquery.colorbox.min.js');

// rewrite if proxy is set
if ($appconf['General']['proxy_handler']) {
	$file_path = str_replace ('files/', 'filemanager/proxy/', $file_path);
}

echo $tpl->render (
	$template,
	array (
		'path' => $file_path,
		'large' => $large,
		'gallery' => str_replace (array ('/', '.', ' '), array ('-', '-', '-'), $path),
		'description' => FileManager::prop ($pruned, 'desc'),
		'desc' => $data['desc']
	)
);

?>
