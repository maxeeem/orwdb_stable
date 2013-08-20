<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<title>ORWDB - Update Manufacturer</title>
<head>

<?php $p = ""; require($p . "styling/header-script.php"); ?>

</head>

<body>

<?php

{# iNCLUDES

require "styling/header.html"; 

require "db/.db-info.php";

require "db/.mysql.php";

}#

{# FUNCTiONS

function dbConnect($dbhost, $dbname) {

	$m = new MongoClient("mongodb://$dbhost");
	
	$db = $m->$dbname;
	
return $db; }

function getSKUs() { global $db, $brand;
	
	$products = $db->products;
	
	$res = $products->find(["Manufacturer" => $brand[0]]);

	foreach ($res as $r) $SKUs[] = $r['ISIS SKU'];

return isset($SKUs) ? $SKUs : array(); }

function getFile() { global $brand;
	
	$file = fopen($_FILES['userfile']['tmp_name'], "r");
	
	$brand = fgetcsv($file);
	
	while ($row = fgetcsv($file)) $SKUs[] = $row[0];
	
return $SKUs; }

}#

{# VARiABLES

$form = <<<EOT
				<div class='body-margin2'>Update a manufacturer.<center>
				<form enctype="multipart/form-data" action="{$_SERVER['PHP_SELF']}" method="POST">
				<input type="hidden" name="MAX_FILE_SIZE" value="200000000" />
				<div class='uploadwrap'><input id='submitbutton' name="userfile" onchange="getfilename()" type="file"/></div>
				<button type='button' class='generalbutton' onclick="document.getElementById('submitbutton').click()" ><div id='filefield'>Choose File</div></button>
				<input id='process' class='generalbutton' onclick='processtext()' type="submit" value="Submit" />
				</form>
				</center>
				
EOT;

$brand = null;

$margin = "<div id='body-margin'>";

$db = dbConnect(DBHOST, DBNAME);

}#

{# MAiN

echo $margin;

if (empty($_FILES)) echo $form;

else {

	$new = getFile();
	
	$old = getSKUs();

	foreach ($new as $newSKU) { if (!in_array($newSKU, $old)) $add[] = $newSKU;	}
	
	foreach ($old as $oldSKU) { if (!in_array($oldSKU, $new)) $remove[] = $oldSKU; }
	
	if (!empty($add)) { echo "To Add"; foreach ($add as $a) echo $a . "</br>"; }
	
	if (!empty($remove)) { echo "To Remove"; foreach ($remove as $r) echo $r . "</br>"; } } 

}#

?>
</div>
</body>
</html>