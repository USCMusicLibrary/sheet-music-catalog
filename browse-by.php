<?php


require_once "functions.php";


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

$browseFacet = "composer_facet";
$namesList = array();
$facets = $searchFacetCounts['facet_fields'][$browseFacet];
for($i=0; $i<sizeof($facets); $i++){
	if ($facets[$i+1]==0){
  	$i++;
    continue;
	}
	else {
		$namesList[] = $facets[$i];
		$i++;
	}
}

natcasesort($namesList);

if (DEBUGGING){
	var_dump($namesList);
}

$azArray = array();
foreach ($namesList as $name){
	if (trim($name)=='') continue;

	$currentLetter = strtoupper(mb_substr($name,0,1));
	if (!array_key_exists($currentLetter,$azArray)){
		//if needed we add letter to array
		$azArray[$currentLetter] = array();
	}

	//add name to letter array
	$azArray[$currentLetter][] = $name;
}

uksort($azArray,"strnatcasecmp");

?>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-12">
				<?php 
				foreach (array_keys($azArray) as $key):?>
					<a href="#<?php print $key;?>"><?php print $key;?></a>&nbsp;
				<?php
				endforeach;
				?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">

		</div>
	</div>
</div> <!-- container-fluid -->
<?php 


?>