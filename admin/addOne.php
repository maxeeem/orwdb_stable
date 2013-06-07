<!-- @Author : Max Poole

@Purpose : See MAiN section, near the bottom of the script 

@Credits : Jeffrey Tu (CSS & jQuery) -->

<?php # REF -- upload.php -- js function addOne()

{# iNCLUDES

require "../db/.db-info.php";

}

{# FUNCTiONS

function dbConnect($dbhost, $dbname) {

	$m = new MongoClient("mongodb://$dbhost");
	
	$db = $m->$dbname;
	
return $db; }

function addCategory($cat) { global $db;
	
	$categories = $db->categories;
	
	$categories->insert(['orw' => $cat]);
	
	echo "Done";
	
return 1; }

function addBrand($brand) { global $db;
	
	$brands = $db->brands;
	
	$brands->insert(['brand' => $brand]);
	
	echo "Done";
	
return 1; }

function addFilter($filter) { global $db;
	
	$filters = $db->filters;

	$filters->insert(['name' => $filter]);
	
	echo "Done";
	
return 1; }

}

{# VARiABLES

$db = dbConnect(DBHOST, DBNAME);

}

{# MAiN

if ($_GET['type'] == 'category') addCategory($_GET['category']);

elseif ($_GET['type'] == 'brand') addBrand($_GET['brand']);

elseif ($_GET['type'] == 'filter') addFilter($_GET['filter']);

// addValues("Finish","Suspension.Shocks", "Blue");

}

?>