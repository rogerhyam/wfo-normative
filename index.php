<?php
require_once('config.php');
require_once('classes/Taxon.php');

// we refuse to render taxa that are synonyms or don't have children
if(isset($_GET['taxon_id'])){
	
	$t = Taxon::factory($_GET['taxon_id']);
	
	// we track the highlight we are after
	if(isset($_GET['highlight'])){
		$highlight = $_GET['highlight'];
	}else{
		$highlight = $t->get_taxon_id();
	}
	
	// it is a synonym
	if($t->get_accepted_taxon()){
		$accepted_taxon = $t->get_accepted_taxon();
		$parent = $accepted_taxon->get_parent();
		$url = "index.php?taxon_id=" . $parent->get_taxon_id() . "&highlight=$highlight";
		header("Location: $url");
		exit;

	}
	
	//it is a has no children
	$kids = $t->get_children();
	if(!$kids || count($kids) < 1){
		$parent = $t->get_parent();
		$url = "index.php?taxon_id=" . $parent->get_taxon_id() . "&highlight=$highlight";
		header("Location: $url");
		exit;
	}
	
	
	
}else{
	$t = false;
}

// sticky search terms
if(isset($_GET['taxon_search'])) $_SESSION["last_search"] = $_GET['taxon_search'];
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>World Flora Online - Normative Taxonomy <?php echo WFO_DEFAULT_YEAR ?></title>

  <!-- jquery stuff -->
  <link rel="stylesheet" href="js/jquery-ui-1.12.1/jquery-ui.min.css">
  <script src="js/jquery-ui-1.12.1/external/jquery/jquery.js"></script>
  <script src="js/jquery-ui-1.12.1/jquery-ui.min.js"></script>
  
  <style>
	  body {
	    font: 16px Arial;
		margin: 0;
	  }
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
	  .wfo_link{
	  	white-space: nowrap;
	  }
	  
	  .search_result{
		  width: 100%;
		  margin-bottom: 1em;
		  margin-left: 1em;
	  }
	  
	  .search_result_full_text{
		  color: gray;
		  font-size: 80%;
	  }
	  
	  #taxon_search{
		  font-size: 110%;
	  }
	  #search_button{
		  font-size: 130%;
	  }
	 
	  #search_instructions{
		  font-size: 80%;
		  color: gray;
		  margin-top: 0.3em;
	  }
	  #warning_banner{
		  width: 100%;
		  font-weight: bold;
		  color: white;
		  background-color: #B73239;
		  padding-top: 0.5em;
		  padding-bottom: 0.5em;
		  padding-left: 1em;
		  
	  }
	  h1{
		  width: 100%;
		  font-weight: bold;
		  color: white;
		  background-color: black;
		  padding: 0.5em;
		  margin-top: 0;
	  }
	  #content{
		  padding: 1em;
	  }
	  #title_banner{
		  width: 100%;
		  position:relative	;
	  }
	  #data_download{
		  display: block;
		  float: right;
		  text-decoration: none;
		  color: gray;
	  }
	  h2 .taxon_rank{
		  color: gray;
		  content-after: ;
	  }
	  h2 .taxon_rank::after{
		  content: ":";
	  }
	  h2 a{
		  font-size: 50%;
		  vertical-align: super;
		  text-decoration: none;
		  width: 3em;
	  }
	  
<?php
if(isset($_GET['highlight'])){
	echo '#' . $_GET['highlight'];
	echo "{background-color: yellow;}";
}
?>
	 
  </style>
  <script src="js/main.js"></script>
  <script>
 	$( function() {
	 	
		$( "#taxon_search" ).autocomplete({
			source: "taxon_autocomplete.php"
		});
		
	<?php
		if(isset($_GET['highlight'])){
			$highlight = $_GET['highlight'];
		    echo "$('html, body').animate({scrollTop: $(\"#$highlight\").offset().top -100 }, 2000);";
		}
	?>		
		
	} );
  </script>
  
</head>
<body>
	<div id="warning_banner">
		Warning: This is a development site and may change or go away without warning.
	</div>
	<h1>World Flora Online - Normative Taxonomy <?php echo WFO_DEFAULT_YEAR ?></h1>
	
	<div id="content">
	
	<form autocomplete="off" action="/index.php">
	  <div class="autocomplete" style="width:80%;">
	    <input id="taxon_search" type="text" name="taxon_search" placeholder="Taxon Name" size="100" <?php echo isset($_SESSION["last_search"]) ? 'value="' . $_SESSION["last_search"] . '"' : ''; ?> />
		&nbsp;
		<input value="Search" id="search_button" type="submit">
	  </div>
	</form>
	<p id="search_instructions">
		Search hints: +include -exclude ~unimportant "exact phrase" wildcar* 
	</p>
	<hr/>

<?php
	
// Do we have a taxon to render?
if($t){
	
	// we have an id so use it.
	echo '<div id="title_banner">';
	echo '<a id="data_download" href="data_download.php?taxon_id='. $t->get_taxon_id() .'">Download CSV: &#x1F4BE;</a>';
	echo "<h2>{$t->get_rank_html()} {$t->get_name_html()} {$t->get_wfo_link(true)}</h2>";
	echo '</div>';
	
	echo '<hr/>';
	echo $t->get_path();
	echo '<hr/>';
	
	$kids = $t->get_children();
	echo '<table class="taxa_list">';
	
	echo '<tr><th>Name</th><th>Authorship</th><th>Published In</th><th>Status</th><th>WFO Link</th></tr>';

	foreach($kids as $kid){
		echo '<tr id="'. $kid->get_taxon_id() .'">';
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
		echo '<td class="wfo_link">'. $kid->get_wfo_link() .'</td>';
		
		echo "</tr>";
		
		if($kid->get_synonyms()){
			$syns = $kid->get_synonyms();
			foreach ($syns as $syn) {
				echo '<tr class="synonym_list_item" id="'. $syn->get_taxon_id() .'" >';
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
				echo '<td class="wfo_link">'. $syn->get_wfo_link() .'</td>';
				echo "</tr>";
			}
		}
		
		
	}
	echo "</table>";
	
}elseif(isset($_GET['taxon_search'])){
	
	$terms = $_GET['taxon_search'];
	
	
	// run the search
	$sql = "SELECT taxonID, scientificName, search_text
 	   	FROM wfo_2019_classification WHERE MATCH(search_text)
		AGAINST('$terms' IN BOOLEAN MODE)
		limit 100";
	$result = $mysqli->query($sql);
	echo $mysqli->error;
	while($row = $result->fetch_assoc()){
		$t = Taxon::factory($row['taxonID']);
		echo $t->get_search_result_html();
	}
	
	// render a search result for each taxon
	
	
	
}else{
	
	// nothing past so don't do search or render taxon - home page
	
	$result = $mysqli->query("SELECT * FROM wfo_2019_classification where taxonRank = 'phylum'");
	echo "<ul>";
	while($row = $result->fetch_assoc()){
		echo "<li>";
		$t = Taxon::factory($row);
		echo $t->get_link_to_taxon();
		echo "</li>";
	}
	echo "</ul>";
	
}

	
?>

</div><!-- end content -->
  
</body>
</html>
