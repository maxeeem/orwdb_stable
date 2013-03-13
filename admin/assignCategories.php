<html>
<title>Assign Categories</title>
<head>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css">
<style>
.ui-menu {
  width: 300px;
}
</style>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>

<script type="text/javascript">

function editCategory(category) {

var edit = document.getElementById("edit")

var msg = "<form action=\"<?php echo $_SERVER['PHP_SELF']; ?>\" method='post'>ORW: " + category + "<br />"

msg += "<input name='orw' type='hidden' value='" + category + "'>"

var amazon_cat = ""

var ebay_cat = ""

if (category in window.categories) {

	amazon_cat = "<input type='text' size='40' name='amazon' value='" + window.categories[category]["amazon"] + "'>"
	
	ebay_cat = "<input type='text' size='40' name='ebay' value='" + window.categories[category]["ebay"] + "'>"
	
	msg += "Amazon: " + amazon_cat + "<br />"

	msg += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;eBay: " + ebay_cat + "<br />"

	msg += "<input type='reset'><input type='submit' value='Save'></form>"

}

else msg = category + " does not have products associated with it</form>"
	
edit.innerHTML = msg

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

function getCategories() {

	global $db;
	
	$categories = $db->categories;
	
	$res = $categories->find();

	foreach ($res as $r) {
		
		$list[$r['orw']]['amazon'] = (array_key_exists('amazon', $r)) ? $r['amazon'] : null;
		
		$list[$r['orw']]['ebay'] = (array_key_exists('ebay', $r)) ? $r['ebay'] : null;
	
	}
	
	ksort($list);

	return $list;

}

function makeList($array) { global $list, $path;

	foreach (array_keys($array) as $child) { if ($child != '') {
		
		$path[] =  $child;
		
		$list .= "<li><a href='#' onclick=\"editCategory('" . implode(".", $path) . "')\">$child</a>";

		if (is_array($array[$child]) && !array_key_exists('', $array[$child])) { 
			
			$list .= "<ul>"; makeList($array[$child]); $list .= "</ul>"; }
			
		$list .= "</li>"; if (count($path) > 1) $last = array_pop($path); }

	}
	if (count($path) == 1) $path = null; 
}

function makeNav($categories) {

	foreach ($categories as $category) {

		$levels = explode(".", $category);
		
		if (count($levels) < 2 || count($levels) > 4) exit("<p>Category $category has more than 4 levels or only the top level</p>");
		
		@$nav[$levels[0]][$levels[1]][$levels[2]][$levels[3]] = null;
		
	}

return $nav; }

function updateCategories($orw, $amazon, $ebay) { global $db;

	$categories = $db->categories;
	
	$categories->update(["orw" => $orw], ['$set' => ["amazon" => $amazon, "ebay" => $ebay]]);
	
	echo '<script type="text/javascript">$("#edit").html("Saved ' . $orw . '");</script>';

}

}

{# VARiABLES

$db = dbConnect(DBHOST, DBNAME);

$path = null;

$edit = "<div id='edit'>edit area</div>";

$list = "<div id='nav'><ul id='menu'>"; 

$categories = getCategories();

/*js categories*/echo "<script type='text/javascript'>categories = " . json_encode($categories) . "</script>";

}

{# MAiN

echo $edit;

if (isset($_POST['orw']) && isset($_POST['amazon']) && isset($_POST['ebay'])) {
	
	updateCategories($_POST['orw'], $_POST['amazon'], $_POST['ebay']);	
	
}

$nav = makeNav(array_keys($categories));

makeList($nav);

echo $list;

echo '</ul></div><script type="text/javascript">$("#menu").menu();</script>';
	
}

?>

</body>
</html>