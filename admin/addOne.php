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

function addCategory($cat) {

	global $db;
	
	$categories = $db->categories;
	
	$categories->insert(['orw' => $cat]);
	
	echo "Done";
	
}

function addBrand($brand, $line) {

	global $db;
	
	$brands = $db->brands;
	
	$brands->insert(['brand' => $brand, 'linecode' => $line]);
	
	echo "Done";
	
}

function addFilter($filter) {

	global $db;
	
	$filters = $db->filters;

	$filters->insert(['name' => $filter]);
	
	echo "Done";
	
}

}

{# VARiABLES

$db = dbConnect(DBHOST, DBNAME);

}

{# MAiN

if ($_GET['type'] == 'category') addCategory($_GET['category']);

elseif ($_GET['type'] == 'brand') addBrand($_GET['brand'], $_GET['line']);

elseif ($_GET['type'] == 'filter') addFilter($_GET['filter']);

}

?>