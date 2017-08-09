<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
/* search-results.php
 * this field performs a search based on parameters in the $_GET request in index.php
 * called by: index.php
 */

//is the search full-text?
$searchQuery['isFullText'] = (isset($_GET['full-text-search'])) ? $_GET['full-text-search'] : false;

$searchQuery['queryArray'] = $queryArray;

$searchQuery['start'] = (isset($_GET['start'])) ? $_GET['start'] : 0;

$searchQuery['rows'] = 20;

$searchQuery['fq'] = (isset($_GET['fq'])) ? $_GET['fq']: array();
$searchQuery['fq_field'] = (isset($_GET['fq_field'])) ? $_GET['fq_field']: array();

try{
$solrResponse = getBrowseResultsFromSolr($searchQuery); //this is where the magic happens
}
catch (Exception $e) {
	print '<h1 class="text-danger text-center">'.$e->getMessage().'</h1>';
	//TODO: email admin to inform that solr is down
	die();
}
//var_dump ($solrResponse);
$searchResponse = $solrResponse['response'];

$searchFacetCounts = $solrResponse['facet_counts'];
$searchHighlighting = $solrResponse['highlighting'];
$searchYearStats = $solrResponse['stats']['stats_fields']['years'];

$minYear = (isset($_GET['minYear']))? $_GET['minYear']: $searchYearStats['min'];
$maxYear = (isset($_GET['maxYear']))? $_GET['maxYear']: $searchYearStats['max'];
$rMinYear = (isset($_GET['rMinYear']))? $_GET['rMinYear']: $searchYearStats['min'];
$rMaxYear = (isset($_GET['rMaxYear']))? $_GET['rMaxYear']: $searchYearStats['max'];

//print ($maxYear);

$oldFq = (isset($_GET['fq'])) ? $_GET['fq']: array();
$oldFqField = (isset($_GET['fq_field'])) ? $_GET['fq_field']: array();

if (count($oldFq)!=count($oldFqField)){
	$oldFq = array();
	$oldFqField = array();
}

$newQuery = $_GET;
$counter=0;
$newFq = array();
$newFqField = array();
foreach ($oldFqField as $fqField){
	if ($fqField!='years'){
		$newFqField[] = $fqField;
		$newFq[] = $oldFq[$counter++];
	}
}

$newQuery['fq'] = $newFq;
$newQuery['fq_field'] = $newFqField;
$newQuery['minYear'] = $minYear;
$newQuery['maxYear'] = $maxYear;


$currentQuery = '//'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.http_build_query($newQuery);

$jsonResponse;

$searchResults = $searchResponse['docs'];



//prints next/previous buttons and total results count
//printResultsNavigation($searchResponse['start'],$searchResponse['numFound'],$searchQuery['rows']);
?>
<script>
var minYear = <?php print $minYear; ?>;
var maxYear = <?php print $maxYear; ?>;
var rMinYear = <?php print $rMinYear; ?>;
var rMaxYear = <?php print $rMaxYear; ?>;
var currentQuery = <?php print '"'.$currentQuery.'"'; ?>;
</script>
<div class="row">
<?php
/*
 * The following displays the facets column
 */
 ?><div class="col-xs-12 col-md-12">
		<div class="col-xs-12"><h4>Browse by:</h4>
			<div class="panel-group" id="accordion">
  <?php
  $counter=1;
  foreach ($facetFields as $facetField => $facetTitle):?>
  <div class="panel panel-default col-xs-6">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle<?php print in_array($facetField,$searchQuery['fq_field'])? ' accordion-opened':'';?>" data-toggle="collapse" href="#collapse<?php print $counter;?>"><?php print $facetTitle?>&nbsp;</a>
      </h4>
    </div>
    <div id="collapse<?php print $counter;?>" class="panel-collapse collapse<?php print in_array($facetField,$searchQuery['fq_field'])? ' in':'';?>">
      <div class="panel-body">
      	<?php

				//TODO: need to get rid of breadcrumb links in browse section
        $currentFacet = $facetField;
      	$facets = $searchFacetCounts['facet_fields'][$currentFacet];
				//sort($facets);
      	for($i=0; $i<sizeof($facets); $i++):
      		if ($facets[$i+1]==0){
      			$i++;
      			continue;
      	  }
      	$isBreadcrumbSet = false;
      	if (in_array($currentFacet, $searchQuery['fq_field'])):
      	  if (in_array('"'.$facets[$i].'"',$searchQuery['fq'])):
      	    $isBreadcrumbSet = true;
      	?>
      	  <a href="<?php print buildFacetBreadcrumbQuery($currentFacet, $facets[$i]);?>"><strong>(X)</strong></a>
      	<?php
      	  endif;
      	endif;?>
      	<a href="<?php print buildFacetFilterQuery($currentFacet, $facets[$i]);?>"><?php print ($isBreadcrumbSet) ? '<strong><em>' : '';?><?php print ($facets[$i]=='')? "None":$facets[$i];?> (<small><strong><?php print $facets[$i+1]; $i++;?></strong></small>)<?php print ($isBreadcrumbSet) ? '</em></strong>' : '';?></a><br>
      	<?php endfor;?>
      </div>
    </div>
  </div>
  <?php
  $counter++;
  endforeach;?>


</div>
		</div>

</div><!-- facets column-->


<?php

/*functions*/
function printResultsNavigation($start,$numFound,$rows){
	?>
	<h3 class="text-right">Showing results <?php print ($start+1)?> to <?php print ($numFound<=$start+$rows ) ?($numFound):($start+$rows );?> of <?php print ($numFound)?></h3>
	<p class="text-right">
	<?php if ($start>0):?>
		<a href="<?php
		$oldQuery = $_GET;
		$oldQuery['start'] = $oldQuery['start']-$rows;
		$newQuery = http_build_query($oldQuery);
		print $_SERVER['PHP_SELF'].'?'.$newQuery?>" class="btn btn-default">Previous</a>
	<?php endif;?>
	<?php if ($numFound>($start+$rows)):?>
		<a href="<?php
		$oldQuery = $_GET;
		$oldQuery['start'] = $oldQuery['start']+$rows;
		$newQuery = http_build_query($oldQuery);
		print $_SERVER['PHP_SELF'].'?'.$newQuery?>" class="btn btn-default">Next</a>
	<?php endif;?>
	</p><?php
}

?>
</div> <!-- row -->
<?php //printResultsNavigation($searchResponse['start'],$searchResponse['numFound'],$searchQuery['rows']);?>
