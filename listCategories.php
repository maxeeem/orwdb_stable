<html>
<title>List Categories</title>
<head>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css">
<style>
.ui-menu {
  width: 300px;
}
</style>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>

</head>
<?php

{# iNCLUDES

require "db/.db-info.php";

}

{# FUNCTiONS

function dbConnect($dbhost, $dbname) {

	$m = new MongoClient("mongodb://$dbhost");
	
	$db = $m->$dbname;
	
	return $db;

}

function getCategories() {

	global $db;
	
	$categories = $db->categories;
	
	$res = $categories->find();

	foreach ($res as $r) $db_categories[] = $r['orw'];

	if (isset($db_categories)) { sort($db_categories); return $db_categories; }
	
	else return array();

}

}

{# VARiABLES

$db = dbConnect(DBHOST, DBNAME);

}

{# MAiN

$validCategories = getCategories();

foreach ($validCategories as $category) {

	$levels = explode(".", $category);
	
	if (count($levels) < 2 || count($levels) > 4) exit("<p>Category $category has more than 4 levels or only top level</p>");
	
	@$nav[$levels[0]][$levels[1]][$levels[2]][$levels[3]] = null;
	
}

foreach ($nav as &$lev0) { // Remove nulls

	foreach ($lev0 as &$lev1) {
	
		if (array_key_exists('', $lev1)) unset($lev1['']);
		
		foreach ($lev1 as &$lev2) {	if (array_key_exists('', $lev2)) unset($lev2['']); }
		
	} 

}

$text = "<ul id='menu'>";

foreach (array_keys($nav) as $level1) {

	$text .= "<li><a href='#'>$level1</a>";
	$text .= "<ul>";
	
	if (is_array($nav[$level1])) { foreach (array_keys($nav[$level1]) as $level2) {
	
		$text .= "<li><a href='#'>$level2</a>";
		$text .= "<ul>";
		
		if (is_array($nav[$level1][$level2])) { foreach (array_keys($nav[$level1][$level2]) as $level3) {
		
			$text .= "<li><a href='#'>$level3</a>";
			$text .= "<ul>";
			
			if (is_array($nav[$level1][$level2][$level3])) { foreach (array_keys($nav[$level1][$level2][$level3]) as $level4) {
			
				$text .= "<li><a href='#'>$level4</a>";
				$text .= "</li>";
			
			} $text .= "</ul>"; }
		
		} $text .= "</ul>"; }
	
	} $text .= "</ul>"; }

} $text .= "</ul>";


}

?>

<body>

<?php echo str_replace("<ul></ul>", "", $text); ?>

<script type="text/javascript">$("#menu").menu();</script>

</body>
</html>