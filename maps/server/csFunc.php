<?php

function validate_bool($data) {	
	foreach ($data as $id => $val) {
		if ($val === "true") {
			$data[$id] = true;
		} else if ($val === "false") {
			$data[$id] = false;
		}
	}

	return $data;
}

function cmp_all($a, $b) {
	//najpierw wg typu
	$type = cmp_type($a, $b);
	if ($type !== 0) return $type;

	//pozniej wg wagi
	$sort = cmp_sort($a, $b);
	if ($sort !== 0) return $sort;

	//jesli typ i waga sa rowne to alfabetycznie
	return cmp_name($a, $b);
}

function cmp_sort($a, $b) {
	if ((int)@$a['sort'] == (int)@$b['sort']) return 0;
	
	return ((int)@$a['sort'] < (int)@$b['sort']) ? -1 : 1;
}

function cmp_type($a, $b) {
	if (@$a['type'] == @$b['type']) return 0;

	if (@$a['type'] == 'Folder') return -1;
	if (@$b['type'] == 'Folder') return 1;

	return 0;
}

function cmp_name($a, $b) {
	return strcasecmp(@$a["name"], @$b["name"]);
}
?>