<!-- @Author : Max Poole

@Purpose : See MAiN section, near the bottom of the script 

@Credits : Jeffrey Tu (CSS & jQuery) -->

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<title>ORWDB - Assign Categories</title>
<head>

<?php $p = "../"; require($p . "styling/header-script.php"); ?>

<script type="text/javascript">

function editCategory(category) {

	var edit = document.getElementById("edit")

	var msg = "<form action=\"<?php echo $_SERVER['PHP_SELF']; ?>\" method='post'>" + category.bold() + "<br /><br />"

	msg += "<input name='orw' type='hidden' value='" + category + "'>"

	var amazon_cat = ""; var ebay_cat = "";	var ebaystore_cat = ""; var placeholder = ""

	if (category in window.categories) {
	
		if (window.categories[category]["amazon"] == '' || window.categories[category]["ebay"] == '' || window.categories[category]["ebaystore"] == '') {
		
			placeholder = "placeholder='Not Specified'" }
		
		var autopart = "<input type='radio' name='product_type' value='AutoPart'"
		
		var accessory = "<input type='radio' name='product_type' value='AutoAccessoryMisc'"
		
		if (window.categories[category]["product_type"] == '' || window.categories[category]["product_type"] == 'AutoPart') { 
		
			autopart += " checked>Auto Parts"; accessory += ">Accessories" }
		
		if (window.categories[category]["product_type"] == 'AutoAccessoryMisc') {	autopart += ">Auto Parts"; accessory += " checked>Accessories"; }
		
		amazon_cat = "<input type='text' size='25' name='amazon' value='" + window.categories[category]["amazon"] + "'" + placeholder + ">"
		
		ebay_cat = "<input type='text' size='25' name='ebay' value='" + window.categories[category]["ebay"] + "'" + placeholder + ">"
		
		ebaystore_cat = "<input type='text' size='25' name='ebaystore' value='" + window.categories[category]["ebaystore"] + "'" + placeholder + ">"
		
		msg += "<div class='assigncategories-name'>Amazon Part Type:</div>" + autopart + "&emsp;" + accessory + "<br />"
		
		msg += "<div class='assigncategories-name'>Amazon:</div>" + amazon_cat + "<br />"
		
		msg += "<div class='assigncategories-name'>eBay:</div>" + ebay_cat + "<br />"

		msg += "<div class='assigncategories-name'>eBay Store:</div>" + ebaystore_cat + "<br /><br />"

		msg += "<div class='marginbutton-left'><input class='generalbutton' type='reset'></div><div class='marginbutton-right'><input class='generalbutton' type='submit' value='Save'></div></form>"

	}

	else msg = category.bold() + "<br>Does not contain products.</form>"
	
	edit.innerHTML = msg

return 1 }

</script>

</head>

<body>

<?php

{# iNCLUDES

require $p . "styling/header.html";

require "../db/.db-info.php";

echo $helptext;  // from header.html

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
		
		$list[$r['orw']]['ebaystore'] = (array_key_exists('ebaystore', $r)) ? $r['ebaystore'] : ""; 
		
		$list[$r['orw']]['product_type'] = (array_key_exists('product_type', $r)) ? $r['product_type'] : ""; 
		
		}
	
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

function updateCategories($orw, $amazon, $ebay, $ebaystore, $product_type) { global $db;

	$categories = $db->categories;
	
	$categories->update(["orw" => $orw], ['$set' => ["amazon" => $amazon, "ebay" => $ebay, "ebaystore" => $ebaystore, "product_type" => $product_type]]);
	
	echo '<script type="text/javascript">$("#edit").html("<strong>' . $orw . '</strong> - Saved");</script>';

return 1; }

}

{# VARiABLES

$db = dbConnect(DBHOST, DBNAME);

$path = null;

$edit = "<div id='edit' class='assigncategories-edit'>Please select a category.</div>";

$margin = "<div id='body-margin'>";

}

{# MAiN

echo $margin; echo $edit;

if (isset($_POST['orw']) && isset($_POST['amazon']) && isset($_POST['ebay']) && isset($_POST['ebaystore']) && isset($_POST['product_type'])) {
	
	updateCategories($_POST['orw'], $_POST['amazon'], $_POST['ebay'], $_POST['ebaystore'], $_POST['product_type']); }
	
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