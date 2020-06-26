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

<?php require_once('look/header_nye.php') ?>

<!-- PlantList: content start -->

<div id="taxonomy_browser">
	
	<form autocomplete="off" action="/index.php">
	  <div class="autocomplete" style="width:100%;">
	    <input id="taxon_search" type="text" name="taxon_search" placeholder="Taxon Name" size="100" <?php echo isset($_SESSION["last_search"]) ? 'value="' . $_SESSION["last_search"] . '"' : ''; ?> />
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
	
	echo $t->get_path();
	
	// we have an id so use it.
	echo '<div id="title_banner">';
	echo '<a id="data_download" href="data_download.php?taxon_id='. $t->get_taxon_id() .'">Download CSV: &#x1F4BE;</a>';
	echo "<h2>{$t->get_rank_html()} {$t->get_name_html()} {$t->get_wfo_link(true)}</h2>";
	echo '</div>';
	
	$kids = $t->get_children();
	
	echo '<span>Table of subtaxa, synonyms and unchecked names</span>';
	echo '<table class="taxa_list">';
	
	echo '<tr><th>Name</th><th>Authorship</th><th>Published In</th><th class="status_cell">Status</th><th>WFO Link</th></tr>';

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
		echo '<td class="status_cell">'. $kid->get_status_html() .'</td>';
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
				echo '<td class="status_cell" >S</td>';
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
 	   	FROM wfo_2019 WHERE MATCH(search_text)
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
	
	$result = $mysqli->query("SELECT * FROM wfo_2019 where taxonRank = 'phylum'");
	
	?>
	
	<table id="intro_table">
		<tr>
			<td>
					<p>The <strong>WFO Plant List</strong> is a static working list of all known plant species produced by the global botanical community. It is derived from a snapshot of the WFO Taxonomic Backbone taken on 17 May 2019. Previous versions can be viewed using the menu bar above. The <strong>WFO Plant List</strong> provides the accepted Latin name for a plant along with all synonyms and publication information. About 20% of names need taxonomic scrutiny and are marked as unchecked. Classification and other details can be seen in the main WFO Portal by following links.</p>
			</td>
			<td id="phylum_list">
				<ul>
					
	<?php
	
	while($row = $result->fetch_assoc()){
		echo "<li>";
		$t = Taxon::factory($row);
		echo $t->get_link_to_taxon();
		echo "</li>";
	}
	
	?>
				</ul>
			</td>
		</tr>
	</table>	
	<?php
	
	
}

	
?>

</div><!-- end #taxonomy_browser -->

<!-- PlantList: content start -->


<?php require_once('look/footer_nye.php') ?>

