<?php

$this->require_admin ();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	$this->redirect ('/properties/admin');
}

$properties = new properties\Properties;
Property::rrmdir ($_SERVER["DOCUMENT_ROOT"].'/files/properties/'.$_POST["id"]);
$properties->remove ($_POST['id']);

if ($properties->error) {
	$this->add_notification (__ ('Unable to delete property.'));
	$this->redirect ('/properties/admin');
}

$this->add_notification (__ ('Property deleted.'));
$this->redirect ('/properties/admin');

?>
