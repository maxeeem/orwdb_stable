<html>
<title>Assign Vehicle Compatibilities</title>
<head>

<?php $p = "../"; require($p . "styling/header-script.php"); ?>

<body>

<?php

{# iNCLUDES

require "../db/.db-info.php";

require $p . "styling/header.html"; 

}

{# FUNCTiONS

function dbConnect($dbhost, $dbname) {

	$m = new MongoClient("mongodb://$dbhost");
	
	$db = $m->$dbname;
	
return $db; }

function validate($i, $start, $end) {

	$date = date("Y") + 2; $err = '';
	
	if (!is_numeric($start) || !is_numeric($end)) $err .= "Year is not numeric in row $i<br />";
	
	if (strlen($start) !== 4 || strlen($end) !== 4) $err .= "Year is not four digits long in row $i<br />";
	
	if ($start < 1900 || $end < 1900) $err .= "Year is less than 1900 in row $i<br />";
	
	if ($start > $date || $end > $date) $err .= "Year is greater than $date in row $i<br />";
	
return $err; }

function getFile() { global $pn, $vehicles;
	
	$file = fopen($_FILES['userfile']['tmp_name'], "r"); $skiprow = fgetcsv($file); $i = 1;
	
	while ($row = fgetcsv($file)) { if (validate($i, $row[1], $row[2]) !== '') $err[] = validate($i, $row[1], $row[2]);
		
		for ($y = $row[1]; $y <= $row[2]; $y++) $years[] = (int) $y;
		
		$vehicles[$row[3]][$row[4]]['Years'][] = $years;
		
		$pn[$row[0]]['Applications'][] = array('Make' => $row[3], 'Model' => $row[4], 'Start year' => $row[1], 'End year' => $row[2], 'Extra' => $row[5]);
		
		unset($years); $i++; }
	
	if (isset($err)) { foreach ($err as $e) echo $e; exit(); }

return 1; }

function getVehicles() { global $db;

	$vehicles = $db->vehicles;
	
	$res = $vehicles->find();

	foreach ($res as $r) {
	
		$validVehicles[$r['Make']][$r['Model']]['Start year'] = $r['Start year'];
		
		$validVehicles[$r['Make']][$r['Model']]['End year'] = $r['End year'];
		
		$validVehicles[$r['Make']][$r['Model']]['Exclude'] = $r['Exclude']; }
	
return (isset($validVehicles)) ? $validVehicles : null; }

function implodeV2($arr) {

	sort($arr);

	while (count($arr) > 0) { 
	
		if (count($arr) > 1) {

			if ($arr[1] !== ($arr[0] + 1)) $res[] = array_shift($arr);
		
			else { $start = array_shift($arr); while (count($arr) > 1 && $arr[1] == ($arr[0] + 1)) array_shift($arr);
				
				$res[] = $start . '-' . array_shift($arr); } } 
		
		else $res[] = array_shift($arr); }
	
return implode(', ', $res); }

function check($vehicles) { global $validVehicles;

	foreach (array_keys($vehicles) as $make) { if (array_key_exists($make, $validVehicles)) { echo "<h2>$make</h2>";
		
		foreach (array_keys($vehicles[$make]) as $model) {
	
			if (array_key_exists($model, $validVehicles[$make])) { $res = $vehicles[$make][$model]['Years'][0];
			
				echo "<h3>$model</h3>";
				
				echo "Years: " . $validVehicles[$make][$model]['Start year'] . '-' . $validVehicles[$make][$model]['End year'] . '<br />';
				
				echo "Exclude: " . implodeV2($validVehicles[$make][$model]['Exclude']) . '<br /><br />';
		
				foreach ($vehicles[$make][$model]['Years'] as $y => $years) { if (array_key_exists(($y + 1), $vehicles[$make][$model]['Years'])) {
					
					$res = array_merge($res, array_diff($vehicles[$make][$model]['Years'][($y + 1)], $res)); } }
				
				sort($res);
				
				foreach ($res as $i => $r) { while (array_key_exists(($i + 1), $res) && ($r + 1) < $res[($i + 1)]) $exclude[] = ++$r; }  

				$start = min($res);
				
				$end = max($res);
				
				if (min($res) < $validVehicles[$make][$model]['Start year']) echo "Range: " . min($res);
				
				else echo "Years: " . $validVehicles[$make][$model]['Start year'];
				
				if (max($res) > $validVehicles[$make][$model]['End year']) echo " - " . max($res) . "<br />";
				
				else echo "-" . $validVehicles[$make][$model]['End year'] . "<br />";
				
				if (!isset($exclude)) $exclude = array('');
				
				echo "Exclude: " . implodeV2($exclude) . '<br />';
				
				echo "<button>Approve</button>";
				
				unset($res); unset($exclude); }
				
			else $addVehicles[$make][$model] = $vehicles[$make][$model]; } }
				
		else $addVehicles[$make] = $vehicles[$make]; }
	
	if (isset($addVehicles)) { echo "<h2>Vehicles not in ORW Database</h2>"; ksort($addVehicles);
	
		foreach (array_keys($addVehicles) as $addmake) { echo "<h3>$addmake</h3>"; ksort($addVehicles[$addmake]);
		
			foreach (array_keys($addVehicles[$addmake]) as $addmodel) { echo "<strong>$addmodel</strong><br />"; $res = $addVehicles[$addmake][$addmodel]['Years'][0];
				
				foreach ($addVehicles[$addmake][$addmodel]['Years'] as $y => $years) { if (array_key_exists(($y + 1), $addVehicles[$addmake][$addmodel]['Years'])) {
					
					$res = array_merge($res, array_diff($addVehicles[$addmake][$addmodel]['Years'][($y + 1)], $res)); } } 
				
				sort($res);
				
				echo "Years: " . min($res) . '-' . max($res) . '<br />';
				
				foreach ($res as $i => $r) { while (array_key_exists(($i + 1), $res) && ($r + 1) < $res[($i + 1)]) $exclude[] = ++$r; }  
			
				if (!isset($exclude)) $exclude = array('');
				
				echo "Exclude: " . implodeV2($exclude) . '<br />';
				
				echo "<button>Insert</button><br />";
				
				unset($res); unset($exclude);	} }
		
		exit(); }

return 1; }

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

$db = dbConnect(DBHOST, DBNAME);

$pn = array();

$vehicles = array();

$validVehicles = getVehicles();

}

{# MAiN

echo "<div id='body-margin'>";

if (empty($_FILES)) echo $form;

else {

	getFile();
	
	check($vehicles);
	
	
	//var_dump($vehicles['Ford']['Ranger']);
	//var_dump($validVehicles);
	//var_dump($pn);
	
}

}

?>

</div>
</body>
</html>