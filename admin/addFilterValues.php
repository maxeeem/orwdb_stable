<?php # REF -- upload.php -- js function addFilterValues()

{# iNCLUDES

require "../db/.db-info.php";

}

{# FUNCTiONS

function dbConnect($dbhost, $dbname) {

	$m = new MongoClient("mongodb://$dbhost");
	
	$db = $m->$dbname;
	
return $db; }

function addValues($f, $c, $v) { global $db;
	
	$categories = $db->categories;
	
	$categories->update(["orw" => $c], ['$addToSet' => ["filters" => $f]]);
	
	$filters = $db->filters;
	
	$checkValues = $filters->find(["name" => $f, "values" => ['$exists' => true]]);
	
	$checkCategory = $filters->find(["name" => $f, "values.category" => $c]);
	
	$values = iterator_to_array($checkValues);
	
	$category = iterator_to_array($checkCategory);
	
	if (empty($values) || empty($category)) $filters->update(["name" => $f], ['$push' => ["values" => ["category" => $c, "values" => [$v]]]]);
	
	else $filters->update(["name" => $f, "values.category" => $c], ['$addToSet' => ["values.$.values" => $v]]);

	echo "Done";
	
return 1; }

}

{# VARiABLES

$db = dbConnect(DBHOST, DBNAME);

}

{# MAiN

addValues($_GET['filter'], $_GET['category'], $_GET['value']);
// addValues("Finish","Suspension.Shocks", "Green");

}

?>