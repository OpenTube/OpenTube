<?php

function get_search_group_file($search_term, $file) {
    if (!file_exists($file)) {
        return [];
    }
	foreach(file($file) as $line) {
        $line = str_replace(array("\r", "\n"), '', $line);
		$group = explode(",", $line);
		if (in_array($search_term, $group)) {
			return $group;
		}
	}
	return [];
}

function get_search_group($search_term) {
    $custom = get_search_group_file($search_term, "custom/search.csv");
    $default = get_search_group_file($search_term, "data/search.csv");
    return array_merge($custom, $default);
}

?>
