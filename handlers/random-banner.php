<?php

/**
 * Random banner embed handler. Chooses a random banner
 * from the specified folder. Used by the WYSIWYG editor's dynamic
 * objects menu, or manually via:
 *
 *     {! properties/random-banner?path=foldername !}
 *
 * The `foldername` is a folder of images inside `/files/`.
 */

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
$image = $files[array_rand($files)];

$width_and_height = getimagesize ($image);
$width_and_height = $width_and_height[3];

$template = 'properties/random-banner';

// rewrite if proxy is set
if ($appconf['General']['proxy_handler']) {
	$image = str_replace ('files/', 'filemanager/proxy/', $image);
}

echo $tpl->render (
	$template,
	array (
		'path' => $image,
		'width_and_height' => $width_and_height
	)
);

?>
