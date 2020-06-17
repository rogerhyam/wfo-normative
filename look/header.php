<!DOCTYPE html>
<html lang="en">
<head>
	
	<meta charset="utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="referrer" content="origin-when-cross-origin"/>
	<link rel="stylesheet" href="http://wfo.nyehughes.webfactional.com/css/style.css">
	<link rel="apple-touch-icon" sizes="180x180" href="http://wfo.nyehughes.webfactional.com/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="http://wfo.nyehughes.webfactional.com/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="http://wfo.nyehughes.webfactional.com/favicon-16x16.png">
	<link rel="manifest" href="http://wfo.nyehughes.webfactional.com/site.webmanifest">
	<link rel="mask-icon" href="http://wfo.nyehughes.webfactional.com/safari-pinned-tab.svg" color="#4a8689">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">

  <title>World Flora Online - Normative Taxonomy <?php echo WFO_DEFAULT_YEAR ?></title>

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
  
</head>
<body class="home">
<header class="main">
<?php require_once('header_nye.php')?>
	
<nav class="main">
	<ul class="nav-items">
		<li><a href="/" class="nav-item">Introduction</a></li>
		<li><a class="nav-item" href="/">June 2019</a></li>
		<li><a class="nav-item" href="/">August 2020</a></li>
		<li><a class="nav-item" href="/">July 2021</a></li>
	</ul>
</nav>
</header>

<!-- end look/header.php -->
