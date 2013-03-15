<html>
<title>Prioritize Filters</title>
<head>

<?php $p = "../"; require($p . "styling/header-script.php"); ?>

<script type="text/javascript">

function showFilters(category) { 

	if (category in window.array) {
		
		$("#selectedCat").html(category.bold())
		
		var values = ''

		for (var i in window.array[category]) values += "<li style='list-style-type: none;' id='filter_" + i + "'>" + window.array[category][i] + "</li>"

		$("#filters").html(values)

		$("#filters").sortable() 
		
		$("#filters").append('<br /><input type="button" id="cancel" value="Cancel" onclick="cancel()">')

		$("#filters").append('<input type="button" id="save" value="Save & Reload" onclick="save(\'' + category + '\')">') }

return 1 }

function cancel() {

	$("#filters").sortable("cancel")

return 1 }

function save(category) {

	var save

	var newOrder = $("#filters").sortable("serialize")

	save = new XMLHttpRequest()

	save.onreadystatechange=function() { if (save.readyState==4 && save.status==200) {
		
			$("#filters").html("Saved " + category.replace('%26', '&'))
			
			window.setTimeout("window.location.reload()", 1500) } }

	category = category.replace(/&/g,'%26')

	save.open("GET", "resort.php?category="+category+"&"+newOrder, true)

	save.send()

return 1 }

</script>

</head>

<body>

<?php

{# iNCLUDES

require $p . "styling/header.html"; 

require "../db/.db-info.php";

}

{# FUNCTiONS

function dbConnect($dbhost, $dbname) {

	$m = new MongoClient("mongodb://$dbhost");
	
	$db = $m->$dbname;
	
return $db; }

function getFilters() { global $db;
	
	$categories = $db->categories;
	
	$res = $categories->find();
	
	foreach ($res as $rows) { if (array_key_exists('filters', $rows) && count($rows['filters']) > 1) { 
	
		$filtersByCategory[$rows['orw']] = $rows['filters']; } }

	ksort($filtersByCategory);
	
return $filtersByCategory; }

function makeList($array) { global $list, $path;

	foreach (array_keys($array) as $child) { if ($child != '') { $path[] =  $child;
		
		$list .= "<li><a href='#' onclick=\"showFilters('" . implode(".", $path) . "')\">$child</a>";

		if (is_array($array[$child]) && !array_key_exists('', $array[$child])) { $list .= "<ul>"; makeList($array[$child]); $list .= "</ul>"; }
			
		$list .= "</li>"; if (count($path) > 1) $last = array_pop($path); } }
		
	if (count($path) == 1) $path = null; 

return 1; }

function makeNav($categories) {

	foreach ($categories as $category) { $levels = explode(".", $category);
		
		if (count($levels) < 2 || count($levels) > 4) exit("<p>Category $category has more than 4 levels or only the top level</p>");
		
		@$nav[$levels[0]][$levels[1]][$levels[2]][$levels[3]] = null; }

return $nav; }

}

{# VARiABLES

$db = dbConnect(DBHOST, DBNAME);

$sortable = '<div id="selectedCat"></div><div id="filtersbox"><ul id="filters">Please select a filter category.</ul></div>';

$path = null;

$margin = "<div id='body-margin'>";

}

{# MAiN

echo $margin;

echo $sortable;

$filters = getFilters();

echo "<script type='text/javascript'>array = " . json_encode($filters) . "</script>";

$nav = makeNav(array_keys($filters));

$list = "<ul id='menu' class='menubrowse'>"; makeList($nav); $list .= "</ul>";

echo $list;

echo '<script type="text/javascript">$("#menu").menu();</script>';

}

?>

</div>
</body>
</html>