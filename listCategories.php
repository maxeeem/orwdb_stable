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

	sort($db_categories);
	
	return $db_categories;

}

}

{# VARiABLES

$db = dbConnect(DBHOST, DBNAME);

}

{# MAiN

$validCategories = getCategories();

foreach ($validCategories as $i => $category) {

	$levels = explode(".", $category);
	
	if (count($levels) < 2 || count($levels) > 4) exit("<p>Category $category has more than 4 levels or only top level</p>");
	
	@$nav[$levels[0]][$levels[1]][$levels[2]][$levels[3]] = null;
	
}



// var_dump($nav);


$list = "<ul id='menu'>";

foreach ($nav as $key0 => $lev0) {
	
	$list .= "<li><a href='#'>$key0</a>";
	
	foreach ($lev0 as $key1 => $lev1) {
	
		if (count($lev1) == 1 && array_key_exists('', $lev1)) $list .= "<ul><li><a href='#'>$key1</a></li></ul></li>";
		
		else {
		
			$list .= "<ul><li><a href='#'>$key1</a><ul>";
		
			foreach ($lev1 as $key2 => $lev2) {
			
				if (count($lev2) == 1 && array_key_exists('', $lev2)) $list .= "<li><a href='#'>$key2</a></li>";
			
				else {
				
					$list .= "<li><a href='#'>$key2</a>";
				
					foreach ($lev2 as $key3 => $lev3) {
				
						if (count($lev3) == 1 && array_key_exists('', $lev3)) $list .= "<li><a href='#'>$key3</a></li></ul></li>";
				
						else {
						
							if ($key3 !== '') { 
							
								if ($lev3 !== null) {	
								
									$list .= "<ul>";
								
									foreach ($lev3 as $key => $value) $list .= "<li><a href='#'>$key</a></li>";

								}
								
								else $list .= "<ul><li><a href='#'>$key3</a></li>";
							
								$list .= "</ul>";
							
							}
							
						}
				
					}
					
					$list .= "</li></ul>";
		
				}
			
			}
			
			$list .= "</li></ul>";
		
		}
	
	}

}

$list .= "</ul>";

}

?>

<body>

<?php echo $list; ?>

<script type="text/javascript">$("#menu").menu();</script>

</body>
</html>