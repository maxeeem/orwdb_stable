<html>
<title>Assign Categories</title>
<head>

<?php $p = "../"; require($p . "styling/header-script.php"); ?>

<script type="text/javascript">

function editCategory(category) {

	var edit = document.getElementById("edit")

	var msg = "<form action=\"<?php echo $_SERVER['PHP_SELF']; ?>\" method='post'>ORW: " + category.bold() + "<br /><br />"

	msg += "<input name='orw' type='hidden' value='" + category + "'>"

	var amazon_cat = ""; var ebay_cat = "";	var ebaystore_cat = ""; var placeholder = ""

	if (category in window.categories) {
	
		if (window.categories[category]["amazon"] == '' || window.categories[category]["ebay"] == '' || window.categories[category]["ebaystore"] == '') {
		
			placeholder = "placeholder='Not Specified'" }

		amazon_cat = "<input type='text' size='40' name='amazon' value='" + window.categories[category]["amazon"] + "'" + placeholder + ">"
		
		ebay_cat = "<input type='text' size='40' name='ebay' value='" + window.categories[category]["ebay"] + "'" + placeholder + ">"
		
		ebaystore_cat = "<input type='text' size='40' name='ebaystore' value='" + window.categories[category]["ebaystore"] + "'" + placeholder + ">"
		
		msg += "Amazon:&nbsp;&nbsp;&nbsp;&nbsp;" + amazon_cat + "<br />"
		
		msg += "eBay:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" + ebay_cat + "<br />"

		msg += "eBayStore:&nbsp;" + ebaystore_cat + "<br /><br />"

		msg += "<input type='reset'><input type='submit' value='Save'></form>"

	}

	else msg = category.bold() + " does not have products associated with it.</form>"
	
	edit.innerHTML = msg

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

function getCategories() { global $db;
	
	$categories = $db->categories;
	
	$res = $categories->find();

	foreach ($res as $r) {
		
		$list[$r['orw']]['amazon'] = (array_key_exists('amazon', $r)) ? $r['amazon'] : "";
		
		$list[$r['orw']]['ebay'] = (array_key_exists('ebay', $r)) ? $r['ebay'] : "";
		
		$list[$r['orw']]['ebaystore'] = (array_key_exists('ebaystore', $r)) ? $r['ebaystore'] : ""; }
	
	ksort($list);

return $list; }

function makeList($array) { global $list, $path;

	foreach (array_keys($array) as $child) { if ($child != '') { $path[] =  $child;
		
		$list .= "<li><a href='#' onclick=\"editCategory('" . implode(".", $path) . "')\">$child</a>";

		if (is_array($array[$child]) && !array_key_exists('', $array[$child])) { $list .= "<ul>"; makeList($array[$child]); $list .= "</ul>"; }
			
		$list .= "</li>"; if (count($path) > 1) $last = array_pop($path); } }
		
	if (count($path) == 1) $path = null; 

return 1; }

function makeNav($categories) {

	foreach ($categories as $category) { $levels = explode(".", $category);	
	
		if (count($levels) < 2 || count($levels) > 4) exit("<p>Category $category has more than 4 levels or only the top level</p>");
		
		@$nav[$levels[0]][$levels[1]][$levels[2]][$levels[3]] = null;	}

return $nav; }

function updateCategories($orw, $amazon, $ebay, $ebaystore) { global $db;

	$categories = $db->categories;
	
	$categories->update(["orw" => $orw], ['$set' => ["amazon" => $amazon, "ebay" => $ebay, "ebaystore" => $ebaystore]]);
	
	echo '<script type="text/javascript">$("#edit").html("Saved <strong>' . $orw . '</strong>");</script>';

return 1; }

}

{# VARiABLES

$db = dbConnect(DBHOST, DBNAME);

$path = null;

$edit = "<div id='edit'>Please select a category.</div>";

$margin = "<div id='body-margin'>";

}

{# MAiN

echo $margin; echo $edit;

if (isset($_POST['orw']) && isset($_POST['amazon']) && isset($_POST['ebay']) && isset($_POST['ebaystore'])) {
	
	updateCategories($_POST['orw'], $_POST['amazon'], $_POST['ebay'], $_POST['ebaystore']); }
	
$categories = getCategories();

/*js categories*/echo "<script type='text/javascript'>categories = " . json_encode($categories) . "</script>";

$nav = makeNav(array_keys($categories));

$list = "<ul id='menu' class='menubrowse'>"; makeList($nav); $list .= "</ul>";

echo $list;

echo '<script type="text/javascript">$("#menu").menu();</script>';
	
}

?>
</div>
</body>
</html>