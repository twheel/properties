<?php

namespace properties;

class Properties extends \Model {
	public $table = 'properties';

	public static function prop_query ($parameters=array (), $ft_parameters=array()) {
		$q = Properties::query ()
			->where ("on_hold = '0'")
			->order ('status')
			->order ('random()');
		foreach ($parameters as $key => $value) {
			$q->where ($key, trim($value));
		}
		foreach ($ft_parameters as $key => $value) {
			$q->where ("$key MATCH '$value'");
		}
		return $q->fetch ();
	}

	public static function sitemap () {
		$properties = Properties::prop_query ();
		foreach ($properties as $property) {
			$urls[] = sprintf ('/%s/%s', \Property::type_to_url ($property->type), $property->id);
		}
		return $urls;
	}
}

?>
