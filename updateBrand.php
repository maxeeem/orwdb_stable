<html>
<title>Update Manufacturer</title>
<head></head>
<body>

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

function getSKUs() {

	global $db, $brand;
	
	$products = $db->products;
	
	$res = $products->find(["Manufacturer" => $brand[0]]);

	foreach ($res as $r) $SKUs[] = $r['ISIS SKU'];

	return isset($SKUs) ? $SKUs : array();

}

function getFile() {
	
	global $brand;
	
	$file = fopen($_FILES['userfile']['tmp_name'], "r");
	
	$brand = fgetcsv($file);
	
	while ($row = fgetcsv($file)) $SKUs[] = $row[0];
	
	return $SKUs;

}

}

{# VARiABLES

$form = <<<EOT
				<br />
				<center><h2>Choose file to upload</h2>
				<br />
				<form enctype="multipart/form-data" action="{$_SERVER['PHP_SELF']}" method="POST">
				<input type="hidden" name="MAX_FILE_SIZE" value="200000000" />
				<input name="userfile" type="file" />
				<input type="submit" value="Submit" />
				</form>
				</center>
EOT;

$brand = null;

$db = dbConnect(DBHOST, DBNAME);

}

{# MAiN

if (empty($_FILES)) echo $form;

else {

	$new = getFile();
	
	$old = getSKUs();

	foreach ($new as $newSKU) { if (!in_array($newSKU, $old)) $add[] = $newSKU;	}
	
	foreach ($old as $oldSKU) { if (!in_array($oldSKU, $new)) $remove[] = $oldSKU; }
	
	if (!empty($add)) { echo "<h3>To Add</h3>"; foreach ($add as $a) echo $a . "</br>"; }
	
	if (!empty($remove)) { echo "<h3>To Remove</h3>"; foreach ($remove as $r) echo $r . "</br>"; }
	
}

}

?>

</body>
</html>