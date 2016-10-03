<?php

/**
 * @file functions.php
* Functions used throughout the website.
*/


require_once('config.php');


function importExcelTabFile(){
	global $mysqli;
	$file = NULL;
	ini_set("auto_detect_line_endings", true);
	try {
	$file = new SplFileObject("smdb_2016_09_30.tsv");
	}
	catch (Exception $error){
		echo '<div class="jumbotron"><h1 class="text-danger">Unable to open uploaded file. Please try again.</h1><p>'.$error->getMessage().'</p></div>';
		return;
	}

	/*
	$ch = curl_init();
	curl_setopt_array($ch, array(
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => $_SERVER['HTTP_HOST'].'/sheetmusic/smdbJune2.txt',
	));

	print  $_SERVER['HTTP_HOST'].'/sheetmusic/smdbJune2.txt';

	$curlResponse = curl_exec($ch);
	if (curl_error($ch)){
		throw new Exception('Unable to read file.');
	}

	//print $curlResponse;
	$lines = explode('\n',$curlResponse);
	print_r ($lines);*/
	$counter=0;

	$counter2=0;

	while ($line= $file->fgets()) {
		
		if ($counter++ == 0) {
			continue; //discard first line because it only contains headers
		}
		//echo $line.'<br>';
		//$line= utf8_encode($line);
		//echo $line.'<br>';
    	$line2 =  preg_replace('/\\t"/',"\t",$line);
		//echo $line2.'<br>';
		$line3 =  preg_replace('/"\\t/',"\t",$line2);
		//echo $line3.'<br>';
		$line4 =  preg_replace('/""/','"',$line3);
		//echo $line4.'<br>';

		$fields = explode("\t",$line4);

		print $fields[5].' '.$counter;

		//function to parse fields with uri data in them
		$parseURIData = function ($rawValue){
			$values = array_filter(explode( ' ; ',trim($rawValue)));
			$finalValues = array();
			foreach ($values as $val){
				$vals = explode('|',$val);
				$fVal = $vals[0];
				$finalValues[] = trim($fVal);
				//$finalValues[] = trim(explode('|',$val)[0]);
			}
			return $finalValues;
		};

		$text_t = array();
		$texts = array_filter(explode( '::',trim($fields[22])));
		foreach ( $texts as $text){
			$newText = trim($text);
			$newText = preg_replace('/\|/',': ',$newText);
			$text_t[] = $newText;
		}

		//print_r( $texts );
		$document = array (
				'id' => $fields[5],
'title' => $fields[23],
'alternative_title' => explode('|',trim($fields[9])),
'composer' => $parseURIData($fields[26]),
//'composer_uri' => $fields[4],
'lyricist' => $parseURIData($fields[28]),
//'lyricist_uri' => $fields[6],
'arranger' => $parseURIData($fields[30]),
//'arranger_uri' => $fields[8],
//'Editors' => $fields[9],
//'Photographers' => $fields[10],
//'Illustrators' => $fields[11],
'publisher' => $fields[31],
'publisher_location' => explode('|',$fields[33]),
'years' =>  parseDate($fields[34]),//$fields[14],
'language' => explode('|',$fields[15]),
'text_t' => $text_t,
'notes' => explode('|',trim($fields[13])),
'donor' => $fields[35],
//'Distributor' => $fields[19],
//'subject_heading' =>  array_filter(explode( '|',trim($fields[20]))),
'subject_heading' => $parseURIData($fields[38]),
'call_number' => $fields[39],
//'PlateNumber' => $fields[23],
'series' => $fields[41],
//'CollectionSource' => $fields[25],
'larger_work' => $fields[43],
//'Keywords' => $fields[27],
//'Original_Notes' => $fields[28],
//'zImagePath' => $fields[29],
//'MidiPath' => $fields[30],
//'zNumberOfPages' => $fields[31],
//'TitleSearchHits' => $fields[32],
//'Incomplete' => $fields[33]
		);

		/*
		$MID = $fields[0];
$Title = $fields[1];
$altTitles = $fields[2];
$composer = $fields[3];
$composer_uri = $fields[4];
$lyricist = $fields[5];
$lyricist_uri = $fields[6];
$arranger = $fields[7];
$arranger_uri = $fields[8];
$Editors = $fields[9];
$Photographers = $fields[10];
$Illustrators = $fields[12];
$Publisher = $fields[13];
$PublisherLocationTest = $fields[14];
$CopyrightDate = $fields[14];
$language = $fields[15];
$text = $fields[16];
$notes = $fields[17];
$Donor = $fields[18];
$Distributor = $fields[19];
$SubjectHeading = $fields[20];
$subjects = $fields[21];
$CallNumber = $fields[22];
$PlateNumber = $fields[23];
$Series = $fields[24];
$CollectionSource = $fields[25];
$LargerWork = $fields[26];
$Keywords = $fields[27];
$Original_Notes = $fields[28];
$zImagePath = $fields[29];
$MidiPath = $fields[30];
$zNumberOfPages = $fields[31];
$TitleSearchHits = $fields[32];
$Incomplete = $fields[33];
*/


		if ($document['id']=="") continue; //skip insert into db if empty
		/*if ($document['id']=='3559') {
			print_r($document);
			return;
		}*/
		//echo ++$counter2.'<br>';
		//print_r($document);
		indexDocument($document);

	}



}

//TODO: Please add error checking!!!
function parseDate($dateString){
	$parts = explode('-',$dateString);
	if (sizeof($parts)==1) {
		return (int)$parts[0];
	}
	else if (sizeof($parts)==2){
		$years = array();
		while ($parts[0]<=$parts[1]){
			$years[] = $parts[0];
			$parts[0]++;
		}
		return $years;
	}
	else return 0;
}


/* function indexDocument($doc){
 * indexes a document into solr
 * does not commit
 *
 * @param {array} $doc:
 *   associative array in the following format:
 *   $doc = array(
 *     'field1' => 'value',
 *     'field2' => array('value1','value2'),
 *     'field3' => 1234,
 *     etc
 *   );
 *   the keys correspond to a field in the solr schema;
 *   values are values to be indexed
 * @return {int}: result value of postJsonDataToSolr();
 *
 */
function indexDocument($doc){
	//print 'indexDocument()<br>';
	$data = array(
			'add' => array (
					'doc' => $doc
			)
	);
	$data_string = json_encode($data);
	print 'curl_exec() done <br>';
	//print_r($doc);
	print '<br>';
	return postJsonDataToSolr($data_string, 'update');
}
/* function commitIndex()
 * commits all pending changes in solr
 * @param {none}
 * @return {int}: result value of postJsonDataToSolr();
 */
function commitIndex(){
	$data = array(
			'commit' => new stdClass()
	);
	$data_string = json_encode($data);
	return postJsonDataToSolr($data_string, 'update');
}
/* function delete_all()
 * deletes all documents in solr
 * @param {none}
 * @return {int}: result value of postJsonDataToSolr();
 */
function delete_all(){
	print 'delete_all();<br>';
	$data = array(
			'delete' => array(
						'query' => '*:*'
					),
			'commit' => new stdClass()
	);
	$data_string = json_encode($data);
	print $data_string;
	return postJsonDataToSolr($data_string, 'update');
}
/* function postJsonDataToSolr($data, $action)
 * posts a json-formatted string to solr
 *
 * @param {string} $data:
 *   json-formatted string, may containg any solr commants, or documents
 * @param {string} $action:
 *   solr handler eg. 'update', 'select'
 * @return {bool}:
 *   returns TRUE is sucessful, otherwise FALSE
 *   sets appropriate global $lastError message
 */
function postJsonDataToSolr($data, $action){
	global $solrUrl;
	$url = $solrUrl.$action;
	print $url;
	//validate json data
	if (json_decode($data)==NULL){
		echo '<div class="col-xs-12"><h1 class="text-danger">postJsonData() invalid Json</h1><p><pre>'.json_last_error().'<br>'.json_last_error_msg().'</pre></p></div>';
		$lastError = 'postJsonDataToSolr(): Invalid Json: '.json_last_error().' - '.json_last_error_msg();
		return false;
	}

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data))
			);

	$result = curl_exec($ch);
	//print_r($data);
	print_r($result);
	return true;
}




/*
 * function getResultsFromSolr
 * performs search on solr and returns mathing documents
 * @param {array} $query: associative array of search parameters
 *  $query['isFullText'] = (bool)
    $query['queryArray'] = array();
    $query['start'] = (int,0);
    $query['rows'] = (int,20);
 *
 * @return {array} or {FALSE} if an error ocurred
 */
function getResultsFromSolr($query){

	$queryString = buildSolrQuery($query);


	$ch = curl_init();
	curl_setopt_array($ch, array(
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => $queryString,
	));

	$jsonResponse = curl_exec($ch);
	if (curl_error($ch)){
		throw new Exception('Unable to connect to search engine.');
	}
	//$jsonResponse = file_get_contents($queryString);

	print $queryString;

	if ($jsonResponse === false) return false;

	$responseArray = json_decode($jsonResponse,true);

	$searchResults = $responseArray/*["response"]*/;


	return $searchResults;

}


/*
 * function buildSolrQuery
 * builds url query for json results from solr based on parameters
 * @param {array} $query: associative array of search parameters
 * @return {string} url-formatted solr query for json-formatted results

 */
function buildSolrQuery($query){

	$queryString = 'q=';

	$queryArray = $query['queryArray'];

	$counter=0;
	foreach ($queryArray as $queryPartial){ //$queryPartial = array($_GET['f'][$counter],$_GET['op'][$counter],$query);
		if ($queryPartial[2]=='') continue; //check it's not empty
		if($counter++ !=0){
			$queryString = $queryString.$queryPartial[1]/*op*/.'+';
		}
		if ($queryPartial[0]=='all'){
			$queryString = $queryString.buildQueryForAllFields($queryPartial[2]);
		}
		else {
			$queryString = $queryString.$queryPartial[0]/*field*/.':('.urlencode($queryPartial[2]).')%0A';
		}


	}

	//filter queries
	$counter=0;
	foreach ($query['fq'] as $fq){
		$queryString = $queryString.'&fq='.urlencode($query['fq_field'][$counter++]).':'.urlencode($fq);
	}


	global $solrUrl;
	global $solrResultsHighlightTag;

	$queryString = $solrUrl
		.'select?'.$queryString.'&start='.$query['start'].'&rows='.$query['rows']
		.'&wt=json&hl=true&hl.simple.pre='.urlencode('<'.$solrResultsHighlightTag.'>')
		.'&hl.simple.post='.urlencode('</'.$solrResultsHighlightTag.'>')
		.'&hl.fl=*&facet=true&facet.field=publisher_facet&facet.field=publisher_location_facet'
		.'&facet.field=language&facet.field=subject_heading_facet&facet.field=composer_facet&facet.field=years&stats=true&stats.field=years&indent=true';

		/*
		 * Archive (Digital collection)
Contributing Institution
Type of content
LC Subject Headings
File Format
Language
Copyright (Use Rights)
Date (slider to select range)
		 * */

	return $queryString;
}


function buildQueryForAllFields($query){
	$queryString = '';
	$searchFields = array(
			'title',
'alternative_title',
'publisher',
'publisher_location',
//'year',
'language',
'text_t',
'notes',
'donor',
'subject_heading',
'call_number',
'series',
'larger_work'
	);
	foreach ($searchFields as $field){
		$queryString = $queryString.$field.':('.urlencode($query);
		if ($field =="title"){
			$queryString = $queryString.')^4%0A';
		}
		else if ($field =="composer"){
			$queryString = $queryString.')^3%0A';
		}
		else if ($field =="text_t"){
			$queryString = $queryString.')^2%0A';
		}
		else {
			$queryString = $queryString.')%0A';
		}
	}
	return $queryString;
}


function buildFacetFilterQuery($facet,$query){
	$newGet = $_GET;
    //$rows = array("start" => "0");
	//print_r($rows);
	//array_merge($rows,$newGet);
	//print_r($newGet);
	$newGet['start'] = 0;
	//print_r($newGet);
	$newQuery = http_build_query($newGet);

	return $_SERVER['PHP_SELF'].'?'.$newQuery.'&fq[]='.urlencode(($query=='')? '""':('"'.escapeDoubleQuotes($query)).'"').'&fq_field[]='.$facet;
}

function escapeDoubleQuotes($string){
	return preg_replace("/\"/", '\\"', $string);
}


function buildFacetBreadcrumbQuery($facet, $query){
	$newGet = array();
	foreach ($_GET as $key => $value){
		$newGet[$key] = $value;
	}
	$new_fq = array();
	$new_fq_field = array();
	$counter=0;
	//debug
	//print_r($newGet);
	foreach ($newGet['fq_field'] as $fq_field){
		//debug
		//print $fq_field.'__'.$newGet['fq'][$counter].'nn'.$query.'--'.'<br>';
		if (!($fq_field==$facet && $newGet['fq'][$counter]=='"'.$query.'"')){
			$new_fq[] = $newGet['fq'][$counter];
			$new_fq_field[] = $fq_field;
		}
		$counter++;
	}

	$newGet['fq_field'] = $new_fq_field;
	$newGet['fq'] = $new_fq;
	//print_r($newGet);
	$newGet['start'] = 0;
	//print_r($newGet);

	//debug
	//print_r($newGet);
	$newQuery = http_build_query($newGet);
	return $_SERVER['PHP_SELF'].'?'.$newQuery;
}

function getImagesForId($id){
	$filePrefix = strval($id).'_';
	$counter=1;
	$fileList = array();
	//print 'sheet-music/'.$filePrefix.strval($counter++);
	while(file_exists('sheet-music/'.$filePrefix.strval($counter).'.jpg')){
		$fileList[] = 'sheet-music/'.$filePrefix.strval($counter++).'.jpg';
	}
	return $fileList;
}

?>
