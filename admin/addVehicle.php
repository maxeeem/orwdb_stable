<!-- @Author : Max Poole

@Purpose : See MAiN section, near the bottom of the script 

@Credits : Jeffrey Tu (CSS & jQuery) -->

<?php # REF -- uploadVehicles.php -- js function addVehicle()

{# iNCLUDES

require "../db/.db-info.php";

}

{# FUNCTiONS

function dbConnect($dbhost, $dbname) {

	$m = new MongoClient("mongodb://$dbhost");
	
	$db = $m->$dbname;
	
return $db; }

function addVehicle($make, $model, $start, $end, $exclude, $skip, $ebaymake = '', $ebaymodel = '', $ebayextra = '', $ebaystart = '', $ebayend = '', $ebayexclude = '') { global $db;

	if ($skip == 'true') $ebay = array("missing");
	
	else $ebay = array('Make' => $ebaymake, 'Model' => $ebaymodel, 'Extra' => $ebayextra, 'Start year' => $ebaystart, 'End year' => $ebayend, 'Exclude' => $ebayexclude);

	$vehicles = $db->vehicles;
	
	$checkVehicle = $vehicles->find(["Make" => $make, "Model" => $model]);
	
	$vehicle = iterator_to_array($checkVehicle);
	
	if (empty($vehicle)) $vehicles->insert(["Make" => $make, "Model" => $model, "Start year" => $start, "End year" => $end, "Exclude" => $exclude, "eBay" => $ebay]);
	
	else $vehicles->update(["Make" => $make, "Model" => $model], ['$set' => ["Make" => $make, "Model" => $model, "Start year" => $start, "End year" => $end, "Exclude" => $exclude, "eBay" => $ebay]]);
	
	echo "Done";
	
return 1; }

}

{# VARiABLES

$db = dbConnect(DBHOST, DBNAME);

}

{# MAiN

if ($_GET['skip'] == 'true') addVehicle($_GET['make'], $_GET['model'], $_GET['start'], $_GET['end'], json_decode($_GET['exclude']), $_GET['skip']);

else addVehicle($_GET['make'], $_GET['model'], $_GET['start'], $_GET['end'], json_decode($_GET['exclude']), $_GET['skip'], $_GET['ebaymake'], $_GET['ebaymodel'], $_GET['ebayextra'], $_GET['ebaystart'], $_GET['ebayend'], json_decode($_GET['ebayexclude']));
// addValues("Finish","Suspension.Shocks", "Green");

}

?>