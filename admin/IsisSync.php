<html>
<title>Isis Sync</title>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
</head>
<body>

<?php

{# iNCLUDES

require "../db/.db-info.php";

require "../db/.mysql.php";

}

{# FUNCTiONS

function dbConnect($dbhost, $dbname) {

	$m = new MongoClient("mongodb://$dbhost");
	
	$db = $m->$dbname;
	
	return $db;

}

function getISIS($linecode) {	global $mysqli;

	$search = "SELECT * FROM `table 1` WHERE `LineCode` = '" . $linecode . "'";

	$res = mysqli_query($mysqli, $search);

	$res->data_seek(0);
	
	while ($row = $res->fetch_assoc()) $ISIS[$row['Part Number']][] = $row;
	
	return $ISIS;

}

function getBrands() { global $db;
	
	$brands = $db->brands;
	
	$res = $brands->find();

	foreach ($res as $r) $db_brands[$r['brand']] = $r['linecode'];

	return isset($db_brands) ? $db_brands : array();

}

function updateProducts($brand) { global $db, $ISIS, $brands;

	$manufacturers = $db->brands;

	$products = $db->products;
	
	$res = $products->find(['Manufacturer' => $brand]);
	
	$missing = array();
	
	foreach ($res as $r) {
		
		$pn = $r['Manufacturer SKU'];
		
		$linecode = $brands[$brand];
		
		//PROTOTYPE array(0 => 'SKU', 1 => 'Price 5', 2 => 'Weight', 3 => 'Length', 4 => 'Width', 5 => 'Height');
	
		$temp = array($pn,'','','','','');
	
		if (array_key_exists($pn, $ISIS[$linecode])) { $missing['partial'][$pn] = $temp; $avail = 0;
			
			foreach ($ISIS[$linecode][$pn] as $co) $avail += $co['Avail']; 
			
			$qty = ($avail < 0) ? 0 : $avail;
			
			$products->update(["Manufacturer SKU" => $pn], ['$set' => ["Quantity Available" => $qty]]);
	
			if ($ISIS[$linecode][$pn][5]['Lbs'] == '0.00') $missing['partial'][$pn][2] = 'X';
			
			else $products->update(["Manufacturer SKU" => $pn], ['$set' => ["Weight" => $ISIS[$linecode][$pn][5]['Lbs']]]);

			if ($ISIS[$linecode][$pn][5]['Height'] == '0.0') $missing['partial'][$pn][5] = 'X';
			
			else $products->update(["Manufacturer SKU" => $pn], ['$set' => ["Height" => $ISIS[$linecode][$pn][5]['Height']]]);
			
			if ($ISIS[$linecode][$pn][5]['Length'] == '0.0') $missing['partial'][$pn][3] = 'X';
			
			else $products->update(["Manufacturer SKU" => $pn], ['$set' => ["Length" => $ISIS[$linecode][$pn][5]['Length']]]);
			
			if ($ISIS[$linecode][$pn][5]['Width'] == '0.0') $missing['partial'][$pn][4] = 'X';
			
			else $products->update(["Manufacturer SKU" => $pn], ['$set' => ["Width" => $ISIS[$linecode][$pn][5]['Width']]]);
			
			if ($ISIS[$linecode][$pn][5]['Price 5'] == '0.00') $missing['partial'][$pn][1] = 'X';
			
			else $products->update(["Manufacturer SKU" => $pn], ['$set' => ["Price 5" => $ISIS[$linecode][$pn][5]['Price 5']]]);

		}
		
		else $missing['full'][] = $pn;
	
	}
	
	if (!empty($missing)) $manufacturers->update(['brand' => $brand], ['$set' => ['Missing' => $missing]]);

}
	
}

{# VARiABLES

$db = dbConnect(DBHOST, DBNAME);

$brands = getBrands();

}

{# MAiN

foreach (array_keys($brands) as $brand) { 

	$ISIS[$brand] = getISIS($brands[$brand]);

	updateProducts($brand);
	
}

// var_dump($ISIS['JAZ']);


}

?>

</body>
</html>