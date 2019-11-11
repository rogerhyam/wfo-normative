<?php

require_once('config.php');
require_once('classes/Taxon.php');
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>World Flora Online - Normative Taxonomy <?php echo WFO_DEFAULT_YEAR ?></title>
  <style>
	  .taxon_status_ACCEPTED{
		  font-weight: bold;
	  }
	  .taxon_status_SYNONYM{
		  color: gray;
	  }
	  
	  .taxon_italic{
		  font-style: italic;
	  }
	  .synonym_list_item_name{
	  	  padding-left: 3em;
	  }
	  th{
		  text-align: left;
	  }
	  
  </style>
  
</head>
<body>
	
	<h1>World Flora Online - Normative Taxonomy <?php echo WFO_DEFAULT_YEAR ?></h1>

<?php
	
// if there  is no taxon_id defined then display the root taxa
if(!isset($_GET['taxon_id'])){
	
	$result = $mysqli->query("SELECT * FROM col_2019.wfo_2019_classification where taxonRank = 'phylum'");
	echo "<ul>";
	while($row = $result->fetch_assoc()){
		echo "<li>";
		$t = Taxon::factory($row);
		echo $t->get_link_to_taxon();
		echo "</li>";
	}
	echo "</ul>";
	
}else{
	
	// we have an id so use it.
	$t = Taxon::factory($_GET['taxon_id']);
	echo "<h2>{$t->get_name_html()}</h2>";
	echo '<hr/>';
	echo $t->get_path();
	echo '<hr/>';
	
	$kids = $t->get_children();
	echo '<table class="taxa_list">';
	
	echo '<tr><th>Name</th><th>Authorship</th><th>Published In</th><th>Status</th><th>WFO Link</th></tr>';
	
	foreach($kids as $kid){
		echo "<tr>";
		echo '<td>';
		if(!$kid->get_children()){
			echo $kid->get_name_html();
		}else{
			echo $kid->get_link_to_taxon();
		}
		echo '</td>';
		echo '<td>';
		echo $kid->get_name_authorship_html();
		echo '</td>';
		echo '<td>';
		if($kid->get_name_publication_html()){
			echo $kid->get_name_publication_html();
		}
		echo '</td>';
		echo '<td>'. $kid->get_status_html() .'</td>';
		echo '<td>'. $kid->get_wfo_link() .'</td>';
		
		echo "</tr>";
		
		if($kid->get_synonyms()){
			$syns = $kid->get_synonyms();
			foreach ($syns as $syn) {
				echo '<tr class="synonym_list_item">';
				echo '<td class="synonym_list_item_name" >';
				echo $syn->get_name_html();
				echo '</td>';
				echo '<td>';
				echo $syn->get_name_authorship_html();
				echo '</td>';
				
				echo '<td>';
				if($syn->get_name_publication_html()){
					echo $syn->get_name_publication_html();
				}
				echo '</td>';
				echo '<td>Synonym</td>';
				echo '<td>'. $syn->get_wfo_link() .'</td>';
				echo "</tr>";
			}
		}
		
		
	}
	echo "</table>";
	
}

	
?>

  
</body>
</html>
