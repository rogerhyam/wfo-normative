<?php

class Taxon{

	protected $row;
	protected $rank;
	protected $taxon_id;
	protected $kids = false;
	protected $parent = false;
	protected $accepted_taxon = false; // holds the accepted taxon if this is a synonym
	protected $synonyms = false;

	function __construct($row){
		$this->row = $row;
		$this->rank = strtoupper($row['taxonRank']);
		$this->taxon_id = $row['taxonID'];
	}

	static function factory($row_or_id){
		
		global $mysqli;
		
		if(!$row_or_id) return null;
		
		if( is_array($row_or_id) ){
			$row = $row_or_id;
		}else{
			$result = $mysqli->query("SELECT * FROM ". WFO_TABLE_NAME ." WHERE taxonID = '$row_or_id';");
			echo $mysqli->error;
			$row = $result->fetch_assoc();
		}
		
		// create an object depending on the rank of the taxon
		switch (strtoupper($row['taxonRank'])) {
			case 'PHYLUM':
				return new Phylum($row);
				break;
			case 'GENUS':
				return new Genus($row);
				break;
			case 'SPECIES':
				return new Species($row);
				break;
			default:
				return new Taxon($row);
				break;
		}
		
		
	}
	
	function get_taxon_id(){
		return $this->taxon_id;
	}
	
	function get_rank(){
		return $this->rank;
	}

	function get_rank_html(){
		return '<span class="taxon_rank">' . ucwords(strtolower($this->rank)) . '</span>';
	}


	function get_name_css_classes(){
		
		$css = array();
		$css[] = 'taxon_name';
		$css[] = 'taxon_rank_'. $this->rank;
		
		switch (strtoupper($this->row['taxonomicStatus'])) {
			case 'SYNONYM':
			case 'HETEROTYPICSYNONYM':
			case 'HOMOTYPICSYNONYM':
				$css[] = 'taxon_status_SYNONYM';
				break;
			case 'ACCEPTED':
				$css[] = 'taxon_status_ACCEPTED';
				break;
			default:
				$css[] = 'taxon_status_UNCHECKED';
				break;
		}
		
		return implode(' ', $css);
	}

	function get_name_html(){
		
      	$css_string = $this->get_name_css_classes();
				
		return "<span class=\"$css_string\">" . $this->row['scientificName'] . "</span>";	
	}
	
	function get_name_authorship_html(){
		return "<span class=\"taxon_name_authorship\">" . $this->row['scientificNameAuthorship'] . "</span>";
	}
	
	function get_name_publication_html(){
		return "<span class=\"taxon_name_published_in\">" . $this->row['namePublishedIn'] . "</span>";
	}
	
	function get_link_to_taxon(){
		
		$name = $this->get_name_html();
		$id = $this->row['taxonID'];
		
		return "<a href=\"index.php?taxon_id=$id\">$name</a>";
	}
	
	function get_parent(){
		
		// only create it once
		if($this->parent) return $this->parent;
		
		$this->parent = Taxon::factory($this->row['parentNameUsageID']);
		
		return $this->parent;
	
	}
	

	function get_accepted_taxon(){
		
		// only create it once
		if($this->accepted_taxon) return $this->accepted_taxon;
		
		if($this->row['acceptedNameUsageID']){
			$this->accepted_taxon = Taxon::factory($this->row['acceptedNameUsageID']);
		}
		
		return $this->accepted_taxon;
		
	}
	
	function get_children(){
		
		global $mysqli;
		
		// only create the list once
		if($this->kids) return $this->kids;
		
		// fetch the rows for the db
		$result = $mysqli->query("SELECT * FROM wfo_2019 where parentNameUsageID = '{$this->taxon_id}' order by scientificName");
		while($kid_row = $result->fetch_assoc()){
			$this->kids[] = Taxon::factory($kid_row);
		}
		return $this->kids;
		
	}
	
	function get_synonyms(){
		
		global $mysqli;
		
		// only create the list once
		if($this->synonyms) return $this->synonyms;
		
		// fetch the rows for the db
		$result = $mysqli->query("SELECT * FROM wfo_2019 where acceptedNameUsageID = '{$this->taxon_id}' order by scientificName");
		while($syn_row = $result->fetch_assoc()){
			$this->synonyms[] = Taxon::factory($syn_row);
		}
		return $this->synonyms;
		
	}
	

	function get_path(){
		$path = array();
		$this->populate_path($path);
		$path[] = '<a href="index.php">Plants</a>';
		$path = array_reverse($path);
		
		return implode(' > ', $path);
	}
	
	function populate_path(&$path){
		
		if(count($path) == 0){
			$path[] = $this->get_name_html();
		}else{
			$path[] = $this->get_link_to_taxon();
		}
		
	
		$p = $this->get_parent();
		
		if($p){
			$p->populate_path($path);
		}
		
		return;
		
	}
	
	function get_status_html(){
		switch ($this->row['taxonomicStatus']) {
			case 'Accepted':
				$status = 'A';
				break;
			case 'Unchecked':
				$status = 'U';
				break;
			case 'Synonym':
				$status = 'S';
				break;
			default:
				$status = $this->row['taxonomicStatus'];
				break;
		}
		return $status;
	}
	
	function get_wfo_link($short = false){
		
		if($short) $txt = 'wfo';
		else $txt = $this->row['taxonID'];
		
		return '<a target="wfo" href="http://www.worldfloraonline.org/taxon/' . $this->row['taxonID'] . '">' . $txt . '</a>';
	}

	function get_search_result_html(){
		
		$out = '<a href="index.php?taxon_id=' . $this->taxon_id . '">';
		$out .= '<div class="search_result">';
		
		$out .= '<div class="search_result_taxon_name">';
		$out .= $this->taxon_id . ' ' .$this->get_name_html() . ' ' . $this->get_name_authorship_html();
		$out .=  '</div>';
		
		$out .= '<div class="search_result_full_text">';
		$out .= $this->row['search_text'];
		$out .=  '</div>';
		
		$out .=  '</div>';
		$out .= '</a>';
		
		return $out;
		
	}

}

class HigherTaxon extends Taxon{
	
	function __construct($row) {
		parent::__construct($row);
	}
	
}

class Phylum extends HigherTaxon{
	
	function __construct($row) {
		parent::__construct($row);
	}
	
}

class Genus extends Taxon{
	
	function __construct($row) {
		parent::__construct($row);
	}
	
	
	
	function get_name_html(){
      	$css_string = $this->get_name_css_classes();
		$css_string .= ' taxon_italic';
		return "<span class=\"$css_string\">" . $this->row['scientificName'] . "</span>";	
	}
	
}

class Species extends Taxon{
	
	function __construct($row) {
		parent::__construct($row);
	}

	function get_name_html(){
      	$css_string = $this->get_name_css_classes();
		$css_string .= ' taxon_italic';
		return "<span class=\"$css_string\">" . $this->row['genus'] . ' ' . $this->row['specificEpithet'] . "</span>";	
	}
	
}

?>