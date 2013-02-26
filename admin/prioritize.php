<html>
<title>Prioritize Filters</title>
<head>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
<script type="text/javascript">

function showFilters(arr) {

var category = document.getElementById("categories")

var now = category.options[category.selectedIndex].text

var filters = document.getElementById("filters")

<!--filters.innerHTML = now-->
var values = ""

for(var i in arr[now]) {

values += "<li id='filter_" + i + "'>" + arr[now][i] + "</li>"
	
}

filters.innerHTML = values

$("#filters").sortable()

}

function cancel() {

$("#filters").sortable("cancel")

}

function save() {

var save

var newOrder = $("#filters").sortable("serialize")

var category = document.getElementById("categories")

var selected = category.options[category.selectedIndex].text

selected = selected.replace(/&/g,'%26')

save = new XMLHttpRequest()

save.onreadystatechange=function() {

  if (save.readyState==4 && save.status==200) window.location.reload();

}

save.open("GET", "resort.php?category="+selected+"&"+newOrder, true);

save.send();

}

</script>
</head>
<body>

<?php

{# iNCLUDES

require "../db/.db-info.php";

}

{# FUNCTiONS

function dbConnect($dbhost, $dbname) {

	$m = new MongoClient("mongodb://$dbhost");
	
	$db = $m->$dbname;
	
	return $db;

}

function getFilters() {

	global $db;
	
	$categories = $db->categories;
	
	$res = $categories->find();
	
	foreach ($res as $rows) { 
		
		if (array_key_exists('filters', $rows) && count($rows['filters']) > 1) {
			
			$filtersByCategory[$rows['orw']] = $rows['filters'];
			
		}

	}

	ksort($filtersByCategory);
	
	return $filtersByCategory;

}

function showCategories($array) {

	$jsfilters = json_encode($array);

	$select = "<select id='categories' onchange='showFilters($jsfilters)'><option value=''>Select Category</option>";

	foreach ($array as $category => $filters) {
	
		$select .= "<option value='$category'>$category</option>";
	
	}
	
	$select .= "</select>";
	
	echo $select;
	
}

}

{# VARiABLES

$db = dbConnect(DBHOST, DBNAME);

$sortable = '<ul id="filters">filters go here</ul>';

$cancel = '<input type="button" id="cancel" value="Cancel" onclick="cancel()">';

$save = '<input type="button" id="save" value="Save & Reload" onclick="save()">';

}

{# MAiN

$filters = getFilters();

// var_dump($filters);

showCategories($filters);

echo $sortable;

echo $cancel;

echo $save;

}

?>

</body>
</html>