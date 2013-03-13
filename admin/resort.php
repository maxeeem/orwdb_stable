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

function resort($category, $newOrder) {

	global $db;
	
	$categories = $db->categories;
	
	$res = $categories->findOne(["orw" => $category]);

	$current = $res['filters'];
	
	foreach ($newOrder as $i => $pos)	$filters[$i] = $current[$pos];
	
	$categories->update(["orw" => $category], ['$set' => ["filters" => $filters]]);
	
	echo "Saved";

}

}

{# VARiABLES

$db = dbConnect(DBHOST, DBNAME);

}

{# MAiN

if (isset($_GET['category']) && isset($_GET['filter'])) { $category = $_GET['category']; $newOrder = $_GET['filter']; }
	
resort($category, $newOrder);

}

?>