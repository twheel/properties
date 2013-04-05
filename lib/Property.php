<?php

class Property {

	public static function clean_image_name ($name, $link_separator="-", $to_lower=false) {
		$name = preg_replace("/[&;'\"[\],<>?\/+!@#$%^*()[:space:]]+/", $link_separator, trim($name));	// can't just do :punct: because we need the .
		$name = preg_replace("/".$link_separator."{2,}/", $link_separator, $name); // "--" -> "-"
		$name = str_replace("$link_separator.", ".", $name); // "-." -> '.'
		if ($to_lower) $name = strtolower($name);
		return $name;
	}

	public static function get_search_parameters ($q) {
		$qsane = Template::sanitize (trim($q));
		list ($type) = Property::url_to_type ($qsane);
		if ($type != 'All' && strtolower($type) != strtolower($qsane)) $qsane .= "* OR $type";
		$qsane .= '*';
		return array ('properties' => $qsane);
	}

	public static function mk_prop_dir ($dir) {
		$path = $_SERVER["DOCUMENT_ROOT"].$dir;
		if (!file_exists($path)) mkdir ($path, 0755);
		return $path;
	}

	public static function move_file ($old_file, $dir, $preface='') {
		if ($old_file) {
			$sroot = $_SERVER["DOCUMENT_ROOT"];
			$name = Property::name_only ($old_file);
			$name = Property::clean_image_name (urldecode ($name));
			if ($preface && substr($name, 0, 1) != $preface) $name = $preface.$name;
			$new_file = "$dir/$name";
			if ($old_file != $new_file && file_exists ($sroot.$old_file)) {
				rename ($sroot.$old_file, $sroot.$new_file);
				FileManager::prop_rename (preg_replace('/^\/files\//', '', $old_file), preg_replace('/^\/files\//', '', $new_file));
			}
			if (file_exists ($sroot.$new_file)) return $new_file;
		}
	}

	public static function move_multiple ($posted_files, $dir) {
		$paths = array();
		if ($posted_files) {
			$files = explode ('|', $posted_files);
			foreach ($files as $file) {
				$path = Property::move_file ($file, $dir, '2');
				if ($path) $paths[] = $path;
			}
		}
		return array_unique($paths);
	}

	public static function name_only ($path) {
		$path_expanded = explode ('/', $path);
		return end ($path_expanded);
	}

    public static function property_types () {
		return array (
			array ('key' => 'Home', 'value' => __ ('Homes')),
			array ('key' => 'Estate', 'value' => __ ('Estates')),
			array ('key' => 'Land', 'value' => __ ('Land'), 'synonyms' => __ ('Acreage')),
			array ('key' => 'Rental', 'value' => __ ('Rentals')),
			array ('key' => 'Commercial', 'value' => __ ('Commercial Properties')),
			array ('key' => 'All', 'value' => __ ('All Properties'))
		);
    }

	public static function rrmdir($path) {
		if (!file_exists($path)) return;
		if (is_file ($path)) @unlink($path);
		else {
			$files = glob ("$path/*");
			foreach ($files as $file) {
				unlink ($file);
				FileManager::prop_delete (preg_replace('/^\/files\//', '', $file));
			}
			rmdir ($path);
		}
	}

	public static function type_to_url ($type) {
		if ($type == 'Home' || $type == 'Rental') $type = $type.'s';
		$type = strtolower ($type);
		return $type;
	}

	public static function url_to_type ($url_type = 'All') {
		if (trim ($url_type) == '') $url_type = 'All';
		$type = ucwords (strtolower ($url_type));
		$property_types = Property::property_types ();
		foreach ($property_types as $array) {
			if (array_search ($type, $array) !== false) {
				return array ($array['key'], $array['value']);
			}
		}
		// return default (last value, 'All')
		return array ($array['key'], $array['value']);
	}

}

?>
