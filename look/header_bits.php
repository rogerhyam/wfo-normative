
<!-- PlantList: header bits start -->
  <title>WFO: Plant List</title>
  <!-- jquery stuff -->
  <link rel="stylesheet" href="js/jquery-ui-1.12.1/jquery-ui.min.css">
  <script src="js/jquery-ui-1.12.1/external/jquery/jquery.js"></script>
  <script src="js/jquery-ui-1.12.1/jquery-ui.min.js"></script>
  <link rel="stylesheet" href="look/style.css">
  <style>
	  
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
<!-- PlantList: header bits end -->
