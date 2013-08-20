<?php # REF -- reviewBrand.php -- js function csv()

{# iNCLUDES

require "db/.db-info.php";

}

{# FUNCTiONS

function dbConnect($dbhost, $dbname) {

	$m = new MongoClient("mongodb://$dbhost");
	
	$db = $m->$dbname;
	
return $db; }

function getBrands($brand) { global $db;
	
	$brands = $db->brands;
	
	$res = $brands->findOne(["brand" => $brand]);
	
return $res['Missing']['partial']; }

}

{# VARiABLES

$db = dbConnect(DBHOST, DBNAME);

$head = array('SKU', 'Price 5', 'Weight', 'Length', 'Width', 'Height');

}

{# MAiN

$brand = $_GET['brand'];

$missing = getBrands($brand);

$csv = fopen("files/missingISIS.csv", "w");

fputcsv($csv, $head);

foreach ($missing as $part) fputcsv($csv, $part);

fclose($csv);

echo "Done";

}

?>