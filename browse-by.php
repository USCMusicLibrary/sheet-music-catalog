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

$browseFacetTitle = isset($_GET['by'])? $_GET['by'] : '';

if ($browseFacetTitle==''):
	$azArray = array();
	$namesList = array();
else:
$browseFacet = array_search($browseFacetTitle,$facetFields);
$namesList = array();
$facets = $searchFacetCounts['facet_fields'][$browseFacet];
for($i=0; $i<sizeof($facets); $i++){
	if ($facets[$i+1]==0){
  	$i++;
    continue;
	}
	else {
		$namesList[$facets[$i]] = $facets[$i+1];
		$i++;
	}
}

//natcasesort($namesList);

if (DEBUGGING){
	//var_dump($namesList);
}

$azArray = array();
foreach ($namesList as $name => $num){
	if (trim($name)=='') continue;

	$currentLetter = strtoupper(mb_substr($name,0,1));
	if (!array_key_exists($currentLetter,$azArray)){
		//if needed we add letter to array
		$azArray[$currentLetter] = array();
	}

	//add name to letter array
	$azArray[$currentLetter][$name] = $num;
}

if (DEBUGGING){
	//var_dump($azArray);
}
uksort($azArray,"strnatcasecmp");

endif;
?>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12">
			<?php foreach ($facetFields as $facetField => $facetTitle):?>
				<div class="col-xs-6 col-md-3" style="padding-bottom:1em;"><a class="btn btn-lg col-xs-12<?php print ($facetTitle==$browseFacetTitle)? ' btn-primary' : ' btn-default' ;?>" href="browse?by=<?php print urlencode ($facetTitle);?>"><?php print $facetTitle;?><hr></a></div>
			<?php endforeach;?>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-8">
				<?php 
				foreach (array_keys($azArray) as $key):?>
					<big><strong><a href="#<?php print $key;?>"><?php print $key;?></a>&nbsp;</strong></big>
				<?php
				endforeach;
				?>
			</div>
			 <div class="col-xs-3 pull-right">
	       <span></span>
				 <script>
				 var browseFacet = '<?php print $browseFacet;?>';
				 </script>
            <input id="nameInput" class="form-control awesomplete pull-right" placeholder="Type a name" list="names-list" name="contributor[]"></input>
              <datalist id="names-list">
    <?php 
    foreach ($namesList as $name => $num):
    if (trim($name)=="") continue;?>
		<option value="<?php print $name; ?>"><?php print $name; ?></option>
    <?php endforeach;?>
</datalist>
       </div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<?php foreach($azArray as $letter => $names):?>
				<div class="clearfix"></div>
				<h1 id="<?php print $letter;?>"><?php print $letter;?></h1>
				<?php 
				$chunkSize = floor(sizeof($names) / 3) + 1;
				$arrays = array_chunk($names,$chunkSize,true);
				foreach($arrays as $nameArray):
				?>
					<div class="col-xs-12 col-md-4">
					<?php foreach ($nameArray as $name => $num):?>
						<p><a href="<?php print $ROOTURL;?>index?op%5B0%5D=AND&q%5B0%5D=%2A&f%5B0%5D=all&form_submitted=&start=0&fq[]=%22<?php print urlencode($name);?>%22&fq_field[]=<?php print $browseFacet; ?>"><?php print $name;?> <strong>(<?php print $num;?>)</strong></a></p>
					<?php endforeach;?>
					</div>
				<?php
				endforeach;?>
			<?php endforeach;?>
		</div>
	</div>
</div> <!-- container-fluid -->
<?php 


?>