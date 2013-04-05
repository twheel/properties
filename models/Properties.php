<?php

namespace properties;

class Properties extends \Model {
	public $table = 'properties';

	public static function prop_query ($parameters=array (), $ft_parameters=array(), $wheres=array(), $orders=array()) {
		$q = Properties::query ();
		foreach ($parameters as $key => $value) {
			$q->where ($key, trim($value));
		}
		foreach ($ft_parameters as $key => $value) {
			$q->where ("$key MATCH '$value'");
		}
		foreach ($wheres as $where) {
			$q->where ($where);
		}
		foreach ($orders as $order) {
			$q->order ($order);
		}
		return $q;
	}

	public static function sitemap () {
		$p = Properties::prop_query ();
		$properties = $p->fetch ();
		foreach ($properties as $property) {
			$urls[] = sprintf ('/%s/%s', \Property::type_to_url ($property->type), $property->id);
		}
		return $urls;
	}
}

?>
