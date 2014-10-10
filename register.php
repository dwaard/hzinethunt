<?php
if (!isset($_COOKIE["group"])) {
	$allgroups = getAllGroupNames();

	//session_start(); // lock other threads 	
	$activegroups = getActiveGroups();
	print_r($activegroups);
	//Bepaal nieuwe groepsnaam
	$newGroup = $allgroups[count($activegroups)];
	echo $newGroup;
	
	$xml=new SimpleXMLElement("<group><name/><completed/><steps/></group>");
	$xml->name = $newGroup;
	$xml->started = date('d/m/Y H:i:s', time());
	//Genereer volgorde
	$numbers = range(0, 5);
	shuffle($numbers);
	foreach ($numbers as $number) {
		$step = $xml->steps->addChild("step");
		$step->number = $number;
		$step->completed = 0;
	}

	$file = "groups/".$newGroup.".xml";
	file_put_contents($file, $xml->asXML());

	setcookie("group", $newGroup);
//	session_write_close(); // From here on out, concurrent requests are no longer blocked
}

header("Location: index.php");

function getActiveGroups() {
	return glob("groups/*.xml");
}

function getAllGroupNames() {
	$handle = fopen("groupnames.txt", "r");
	if ($handle) {
		while (($buffer = fgets($handle, 4096)) !== false) {
			$result[] = str_replace(array("\r", "\n"), '', $buffer);
		}
		if (!feof($handle)) {
			echo "Error: unexpected fgets() fail\n";
		}
		fclose($handle);
	}
	return $result;
}
?>