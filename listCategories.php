<html>
<title>List Categories</title>
<head>

<?php $p = ""; require($p . "styling/header-script.php"); ?>

</head>

<?php

{# iNCLUDES

require "db/.db-info.php";

}

{# FUNCTiONS

function dbConnect($dbhost, $dbname) {

	$m = new MongoClient("mongodb://$dbhost");
	
	$db = $m->$dbname;
	
return $db; }

function getCategories() { global $db;
	
	$categories = $db->categories;
	
	$res = $categories->find();

	foreach ($res as $r) $db_categories[] = $r['orw'];

	if (isset($db_categories)) { sort($db_categories); return $db_categories; }
	
	else return array();

}

function makeList($array) { global $list;

	foreach (array_keys($array) as $child) { if ($child != '') { $list .= "<li><a href='#'>$child</a>";

		if (is_array($array[$child]) && !array_key_exists('', $array[$child])) { $list .= "<ul>"; makeList($array[$child]); $list .= "</ul>"; }
			
		$list .= "</li>"; }	}
	
return 1; }

function makeNav($validCategories) {

	foreach ($validCategories as $category) { $levels = explode(".", $category);
		
		if (count($levels) < 2 || count($levels) > 4) exit("<p>Category $category has more than 4 levels or only top level</p>");
		
		@$nav[$levels[0]][$levels[1]][$levels[2]][$levels[3]] = null;	}

return $nav; }

}

{# VARiABLES

$db = dbConnect(DBHOST, DBNAME);

$validCategories = getCategories();

}

{# MAiN

$nav = makeNav($validCategories);

$list = "<div class='catb'><ul id='catbrowser'><li><a href=''>Browse Categories</a><ul>"; makeList($nav); $list .= "</ul></li></ul></div><hr>";

}

?>

<body>

<?php echo $list; ?>

</body>
</html>