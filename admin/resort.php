<?php

{# iNCLUDES

require "../db/.db-info.php";

}

{# FUNCTiONS

function dbConnect($dbhost, $dbname) {

	$m = new MongoClient("mongodb://$dbhost");
	
	$db = $m->$dbname;
	
return $db; }

function resort($category, $newOrder) { global $db;
	
	$categories = $db->categories;
	
	$res = $categories->findOne(["orw" => $category]);

	$current = $res['filters'];
	
	foreach ($newOrder as $i => $pos)	$filters[$i] = $current[$pos];
	
	$categories->update(["orw" => $category], ['$set' => ["filters" => $filters]]);
	
	echo "Saved";

return 1; }

}

{# VARiABLES

$db = dbConnect(DBHOST, DBNAME);

}

{# MAiN

// addValues($_GET['filter'], $_GET['category'], $_GET['value']);

if (isset($_GET['category'])) $category = $_GET['category'];

if (isset($_GET['filter'])) $newOrder = $_GET['filter'];

// $category = "Performance & Filters.Mufflers";

// $newOrder = [1,0];

resort($category, $newOrder);

}

?>