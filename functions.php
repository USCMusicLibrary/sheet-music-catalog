<?php

/**
 * @file functions.php
* Functions used throughout the website.
*/


require_once('config.php');
require_once('db-config.php');


function importExcelTabFile(){
  global $mysqli;
  $file = NULL;
  ini_set("auto_detect_line_endings", true);
  try {
  $file = new SplFileObject("uploads/sm_db_backup.tsv");
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

    print $fields[1].' '.$counter;

    //function to parse fields with uri data in them
    $parseURIData = function ($rawValue){
      $values = array_filter(explode( ' ; ',trim($rawValue)));
      $finalValues = array();
      foreach ($values as $val){
        $vals = explode('|',$val);
                $nVal = trim($vals[0]);
                $uVal = trim($vals[1]);
        $finalValues[] = array($nVal, $uVal);
      }
      return $finalValues;
    };

    $text_t = array();
    $texts = array_filter(explode( '::',trim($fields[8])));
    foreach ( $texts as $text){
      $newText = trim($text);
      $newText = preg_replace('/\|/',': ',$newText);
      $text_t[] = $newText;
    }

    //print_r( $texts );
    $document = array (
        'id' => $fields[1],
'title' => $fields[9],
'alternative_title' => explode('|',trim($fields[5])),
'composer' => $parseURIData($fields[10]),
//'composer_uri' => $fields[4],
'lyricist' => $parseURIData($fields[11]),
//'lyricist_uri' => $fields[6],
'arranger' => $parseURIData($fields[12]),
//'arranger_uri' => $fields[8],
'editor' => $parseURIData($fields[2]),
'photographer' => $parseURIData($fields[3]),
'illustrator' => $parseURIData($fields[4]),
'publisher' => explode('|',$fields[13]),
'publisher_location' => explode('|',$fields[14]),
'years' =>  parseDate($fields[15]),//$fields[14],
'years_text' => trim($fields[15]),
'language' => explode('|',$fields[7]),
'text_t' => $text_t,
'notes' => explode('|',trim($fields[6])),
'donor' => $fields[16],
//'Distributor' => $fields[19],
//'subject_heading' =>  array_filter(explode( '|',trim($fields[20]))),
'subject_heading' => $parseURIData($fields[18]),
'call_number' => $fields[19],
//'PlateNumber' => $fields[23],
'series' => $fields[21],
'collection_source' => $fields[22],
'larger_work' => $fields[23],
'has_image' => (idHasImage($fields[1]))?"Online score" : "Print only"
//'Keywords' => $fields[27],
//'Original_Notes' => $fields[28],
//'zImagePath' => $fields[29],
//'MidiPath' => $fields[30],
//'zNumberOfPages' => $fields[31],
//'TitleSearchHits' => $fields[32],
//'Incomplete' => $fields[33]
    );


    if ($document['id']=="") continue; //skip insert into db if empty

    //print_r($document);

    //we need to modify the $document object before we feed it to solr
    //so it indexes correctly
    $solrDocument = $document;
    global $contribtypes;
    foreach (array_keys($contribtypes) as $ctype) {
      //sanity check
      if (!array_key_exists($ctype,$solrDocument)) continue;
      $contributors = $solrDocument[$ctype];
      $newContributors = array();
      foreach ($contributors as $contributor){
        $newContributors[] = $contributor[0];//we only need name for solr, not uri
      }
      $solrDocument[$ctype] = $newContributors;
    }
    if(array_key_exists('subject_heading', $solrDocument)){
      $newSubjects = array();
      $subjects = $solrDocument['subject_heading'];
      foreach($subjects as $subject){
        $newSubjects[] = $subject[0];
      }
      $solrDocument['subject_heading'] = $newSubjects;
    }
    //send modified doc to solr
    indexDocument($solrDocument);

    //send unmodified document to database
    insertDocDb($document,'approved');

    //add addVocabularies
    addVocabularies($document);

    flush();
  }
}


function insertDocDb($doc,$status){
  global $mysqli;
  
  $mid = $doc['id'];
  $title = $doc['title'];
  //$publisher = $doc['publisher'];
  $call_number = $doc['call_number'];
  $series = $doc['series'];
  $larger_work = $doc['larger_work'];
  $collection_source = $doc['collection_source'];
  $donor = $doc['donor'];
  $scanning_technician = array_key_exists('scanning_technician',$doc)?$doc['scanning_technician']:"";
  $media_cataloguer =  array_key_exists('media_cataloguer',$doc)?$doc['media_cataloguer']:"";
  $reviewer = array_key_exists('reviewer',$doc)?$doc['reviewer']:"";

  $statement = $mysqli->prepare("INSERT INTO records (mid,title,call_number,series,larger_work,collection_source,donor,scanning_technician,media_cataloguer,reviewer,status,date_created,date_modified)"
                  ." VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())");
  //var_dump($doc);
  $statement->bind_param("issssssssss",$mid, 
              $title, 
              $publisher, 
              $call_number, 
              $series, 
              $larger_work, 
              $collection_source, 
              $donor, 
              $scanning_technician, 
              $media_cataloguer, 
              $reviewer,
              $status);
  $statement->execute();
  $statement->store_result();

  print($mysqli->error);

  $recordID = $mysqli->insert_id;

  foreach ($doc['alternative_title'] as $alternative_title){
    $statement = $mysqli->prepare("INSERT INTO alternative_titles (record_id,alternative_title)"
                  ." VALUES (?,?)");
    $statement->bind_param("is", $recordID,$alternative_title);
    $statement->execute();
    $statement->store_result();
  }

  foreach ($doc['notes'] as $note){
    $statement = $mysqli->prepare("INSERT INTO notes (record_id,note)"
                  ." VALUES (?,?)");
    $statement->bind_param("is", $recordID,$note);
    $statement->execute();
    $statement->store_result();
  }

  foreach ($doc['text_t'] as $text){
    $statement = $mysqli->prepare("INSERT INTO texts (record_id,text_t)"
                  ." VALUES (?,?)");
    $statement->bind_param("is", $recordID,$text);
    $statement->execute();
    $statement->store_result();
  }

  foreach ($doc['publisher_location'] as $publisher_location){
    $statement = $mysqli->prepare("INSERT INTO publisher_locations (record_id,publisher_location)"
                  ." VALUES (?,?)");
    $statement->bind_param("is", $recordID,$publisher_location);
  $statement->execute();
    $statement->store_result();
  }

  foreach ($doc['language'] as $language){
    $statement = $mysqli->prepare("INSERT INTO languages (record_id,language)"
                  ." VALUES (?,?)");
    $statement->bind_param("is", $recordID,$language);
  $statement->execute();
    $statement->store_result();
  }
  foreach ($doc['publisher'] as $publisher){
    $statement = $mysqli->prepare("INSERT INTO publishers (record_id,publisher)"
                  ." VALUES (?,?)");
    $statement->bind_param("is", $recordID,$publisher);
  $statement->execute();
    $statement->store_result();
  }
  


    }

function addVocabularies($doc){
    global $mysqli;
    global $contribtypes;
global $other_heading_types;
    $contributor_headings = array_keys($contribtypes);
    $headingtypes = array_merge($contributor_headings, $other_heading_types);
    foreach ( $headingtypes as $htype) {
      if (!array_key_exists($htype,$doc)) continue;
            foreach($doc[$htype] as $heading){
            $label = $heading[0];
            $uri = $heading[1];
            if($htype == 'subject_heading'){
                    $table = 'subject_headings';
                    $labelfield = 'subject_heading';
                    $crosstable = 'has_subject';
                    $crossID = 'subject_id';
                    #Any additional supported vocabularies will follow as else ifs here
            }
            else{
                    $table = 'names';
                    $roleID = $contribtypes[$htype];
                    $labelfield = 'name';
                    $crosstable = 'contributors';
                    $crossID = 'contributor_id';
            }
            $query = "SELECT id, uri FROM ".$table." WHERE ".$labelfield." = ? LIMIT 1";
            $statement = $mysqli->prepare($query);
            //print $query.'<br>';
            //print $mysqli->error;
            
            //declaring $localID in this scope
            $localID = null;

            if (!$statement) {
              print 'Complex field update error: prepared statement failed<br>';
              print $mysqli->error;
            }
            else {
                    $statement->bind_param('s', $label);
                    $statement->execute();
                    $statement->store_result();
                    $statement->bind_result($resultID, $localUri);
                    if ($statement->fetch()) {
                      $localID = $resultID;
                      //if we have a match then we have to
                      //check if uris are different or if new one is blank
                      //then write problem note
                      if ($uri != $localUri || trim($uri) =="") {
                            #for typos, etc.; there is probably a better way of handling this
                            $query = "UPDATE ".$table." SET problem_note = ? WHERE id = ?";
                            $statement = $mysqli->prepare($query);
                            if (!$statement){
                                    print "Differing URI prepare statement failed";
                                    print_r( $statement->error_list) ;
                            }
                            else{
                              $newURI = "Diff:".$uri;
                              $statement->bind_param('si', $newURI, $localID);
                              $statement->execute();
                              $statement->store_result();
                              //$localID = $statement->insert_id;
                            }
                        }
                    }
                    else {
                      //if no match then we just insert into table
                        $statement = $mysqli->prepare("INSERT INTO ".$table." (".$labelfield.", uri) VALUES(?,?)");
                        $statement->bind_param('ss', $label, $uri);
                        $statement->execute();
                        $statement->store_result();
                        $localID = $statement->insert_id;
                    }
                }
                #Update crosstable
                print("\n\t".$htype  . ': ' . $label ." (LOCAL ID: " . $localID. ') ; URI: ' . $uri . ' </br>');
                if ($crosstable == 'contributors'){
                    $sql = "INSERT INTO ".$crosstable." (record_id,".$crossID.",role_id) VALUES (?,?,?)";
                    $params = [&$recordID, &$localID, &$roleID];
                    $type = 'iii';
                    
                } else {
                    $sql = "INSERT INTO ".$crosstable." (record_id,".$crossID.") VALUES (?,?)";
                    $params = [&$recordID, &$localID];
                    $type = 'ii';
                }
                if ($contribstmt = $mysqli->prepare($sql)) {
                       call_user_func_array(array($contribstmt, "bind_param"), array_merge(array($type), $params));
                       $contribstmt->execute();
                       $contribstmt->store_result();
                } else {
                    print "DIED:\n";
                    die("Errormessage: " . $mysqli->error);
                }     
                }
            }
}

//print("</br>");

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

  //print $queryString;

  if ($jsonResponse === false) return false;

  $responseArray = json_decode($jsonResponse,true);

  $searchResults = $responseArray/*["response"]*/;


  return $searchResults;

}

function getBrowseResultsFromSolr($query){
  $queryString = buildSolrQuery($query)."&facet.limit=-1";


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
    //make sure to undo url encoding
    $queryPartial[2] = urldecode($queryPartial[2]);

		if($counter++ !=0){
			$queryString = $queryString.$queryPartial[1]/*op*/.'+';
		}
		if ($queryPartial[0]=='all'){
			$queryString = $queryString.buildQueryForAllFields($queryPartial[2]);
		}
		else if ($queryPartial[0]=='contributor'){//temporary
			$queryString = $queryString.buildQueryForContributors($queryPartial[2]);
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
    .'&hl.fl=*&facet=true';

global $facetFields;
foreach ($facetFields as $key=>$val){
  $queryString = $queryString.'&facet.field='.$key;
    /*'&facet.field=publisher_facet&facet.field=publisher_location_facet'
    .'&facet.field=language&facet.field=subject_heading_facet&facet.field=composer_facet'
    .'&facet.field=years&facet.field=arranger_facet&facet.field=illustrator_facet&facet.field=lyricist_facet&stats=true&stats.field=years&indent=true';*/
}

$queryString = $queryString.'&stats=true&stats.field=years&indent=true';
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

function buildQueryForContributors($query){
	$queryString = '';
	global $contribtypes;
	foreach ($contribtypes as $field=>$value){
		$queryString = $queryString.$field.':('.urlencode($query).')%0A';
	}
	return $queryString;
}

/* function buildQueryForAllFields($query)
 * builds a solr query when "search all fields" is selected
 * also adds weight to certain fields
 * @param {string} $query:
 *   query value
 * @return {string}: a solr query that will search all fields for $query
 */
function buildQueryForAllFields($query){
  $queryString = '';
  global $searchFields;
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

/* function buildFacetFilterQuery($facet,$query)
 * builds a facet filter query based of the current search terms
 * and the corresponding facet and query
 *
 * @param {string} $facet:
 *   facet to narrow down by
 * @param {string} $query:
 *   value to filter by
 * @return {string}: href-ready value for a filter query link
 */
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


/* function buildFacetBreadcrumbQuery($facet, $query){
 * builds a breadcrumb href to undo a given facet filter query
 *
 * @param {string} $facet:
 *   facet to narrow down by
 * @param {string} $query:
 *   value to filter by
 * @return {string}: href-ready value for a breadbrumb filter query link
 */
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

function idHasImage($id){
  $fileList = getImagesForId($id);
  if (empty($fileList)){
    return false;
  }
  else return true;
}

?>
