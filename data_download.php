<?php
	
require_once('config.php');

$taxon_id = $_GET['taxon_id'];

if(!$taxon_id || !preg_match('/^wfo-[0-9]{10}$/', $taxon_id)){
	echo "This doesn't look like a well formed WFO identifier";
	exit;
}

$sql = "SELECT * FROM wfo_2019 where parentNameUsageID = '$taxon_id'
union
SELECT * FROM  wfo_2019 WHERE acceptedNameUsageID in (SELECT taxonID FROM wfo_2019 where parentNameUsageID = '$taxon_id' )";

$result = $mysqli->query($sql);

$output = fopen("php://output",'w') or die("Can't open php://output");
header("Content-Type:application/csv");
header("Content-Disposition:attachment;filename=" . $taxon_id . '-' . WFO_DEFAULT_YEAR . '.csv'); 

// write out the header row
$cols = $result->fetch_fields();
$header = array();
foreach ($cols as $col) {
	$header[] = $col->name;
}
fputcsv($output, $header);

while($row = $result->fetch_array(MYSQLI_NUM)){
	fputcsv($output, $row);
}

fclose($output) or die("Can't close php://output");

?>