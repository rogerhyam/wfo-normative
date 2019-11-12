<?php

require_once('config.php');

$term = $_GET['term'] . '%';

$out = array();

$sql = "SELECT distinct(scientificName) FROM ". WFO_TABLE_NAME ." WHERE scientificName LIKE '$term' ORDER BY scientificName ASC LIMIT 100";
$result = $mysqli->query($sql);
while($row = $result->fetch_assoc()){
	$out[] = $row['scientificName'];
}

$json = json_encode($out);
//header('Content-Type: application/json');
echo $json;
?>