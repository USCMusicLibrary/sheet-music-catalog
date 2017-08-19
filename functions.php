<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
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

  $callNumbers = array();
  $callNumberErrors = array();

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
'has_image' => (idHasImage($fields[1]))?"Online score" : "Print only",
'scanning_technician' => '',
'media_cataloguer_id' => NULL,
'reviewer_id' => NULL,
'admin_notes' => ''
//'Keywords' => $fields[27],
//'Original_Notes' => $fields[28],
//'zImagePath' => $fields[29],
//'MidiPath' => $fields[30],
//'zNumberOfPages' => $fields[31],
//'TitleSearchHits' => $fields[32],
//'Incomplete' => $fields[33]
    );


    if ($document['id']=="") continue; //skip insert into db if empty


    //call number insert into correct json file
    $document['call_number'] = trim($document['call_number']);
    print '<br>';
    $num = 0;
    try{
      preg_match('/^[\D\s]+/',$document['call_number'],$match);
      if (!isset($match[0])) throw new Exception("Notice: Undefined offset: 0 in collection");
      $collection = trim($match[0]);
      $num;
      preg_match('/[\d]+$/',$document['call_number'],$match);
      if (!isset($match[0])) throw new Exception("Notice: Undefined offset: 0 in number");
      $num = $match[0];
    }
    catch (Exception $e) {
      print 'Call number error: '.  $e->getMessage()."  On call number: ". $document['call_number'].'<br>';
      $callNumberErrors[$document['id']] = $document['call_number']; 
      //die();
    }
    $callNumbers[$collection][] = $num;

//continue;
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
    $solrDoc = array();
    global $solrFieldNames;
    foreach ($solrDocument as $key => $val){
      if (array_key_exists($key,$solrFieldNames)){
        $solrDoc[$key] = $val;
      }
    }

    //send modified doc to solr
    indexDocument($solrDoc);

    //send unmodified document to database
    $recordID = insertDocDb($document,'approved');

    //add addVocabularies
    addVocabularies($document, $recordID);

    flush();
  }

  foreach ($callNumbers as $key=>$values){
    $jsonValues = json_encode($values);
    file_put_contents("data/cat-".$key.".json",$jsonValues);
  }

  file_put_contents("data/callNumberErrors.json",json_encode($callNumberErrors));
}


function insertDocDb($doc,$status,$dateCreated=0){
  global $mysqli;
  
  $mid = $doc['id'];
  $id = $mid;
  $title = $doc['title'];
  //$publisher = $doc['publisher'];
  $call_number = $doc['call_number'];
  $series = $doc['series'];
  $larger_work = $doc['larger_work'];
  $collection_source = $doc['collection_source'];
  $donor = $doc['donor'];
  $scanning_technician = $doc['scanning_technician'];
  $media_cataloguer =  $doc['media_cataloguer_id'];
  $reviewer = $doc['reviewer_id'];
  $admin_notes = $doc['admin_notes'];

  if(is_array($doc['years'])){
   $startyear = $doc['years'][0];
   $endyear = array_pop($doc['years']);     
  }
  else {
      $startyear = $doc['years'];
      $endyear = $doc['years'];
  }
  
  if($dateCreated==0){//new record
    $statement = $mysqli->prepare("INSERT INTO records (id,mid,title,call_number,series,larger_work,collection_source,donor,scanning_technician,media_cataloguer_id,reviewer_id,admin_notes,status,date_created,date_modified,start_year, end_year)"
                  ." VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW(),?,?)");
      $statement->bind_param("iisssssssiissii",$id, $mid, 
              $title, 
              $call_number, 
              $series, 
              $larger_work, 
              $collection_source, 
              $donor, 
              $scanning_technician, 
              $media_cataloguer, 
              $reviewer,
              $admin_notes,
              $status,
              $startyear,
              $endyear);
  }
  else {//update existing record
    $statement = $mysqli->prepare("INSERT INTO records (id,mid,title,call_number,series,larger_work,collection_source,donor,scanning_technician,media_cataloguer_id,reviewer_id,admin_notes,status,date_created,date_modified,start_year,end_year)"
                  ." VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?,?)");
      $statement->bind_param("iisssssssiisssii",$id, $mid, 
              $title, 
              $call_number, 
              $series, 
              $larger_work, 
              $collection_source, 
              $donor, 
              $scanning_technician, 
              $media_cataloguer, 
              $reviewer,
              $admin_notes,
              $status,
              $dateCreated,
              $startyear,
              $endyear);
  }
  //var_dump($doc);

  
  $statement->execute();
  $statement->store_result();

  print($mysqli->error);

  $recordID = $mysqli->insert_id;

  foreach ($doc['alternative_title'] as $alternative_title){
    if (trim($alternative_title)=="") continue;
    $statement = $mysqli->prepare("INSERT INTO alternative_titles (record_id,alternative_title)"
                  ." VALUES (?,?)");
    $statement->bind_param("is", $recordID,$alternative_title);
    $statement->execute();
    $statement->store_result();
  }

  foreach ($doc['notes'] as $note){
    if (trim($note)=="") continue;
    $statement = $mysqli->prepare("INSERT INTO notes (record_id,note)"
                  ." VALUES (?,?)");
    $statement->bind_param("is", $recordID,$note);
    $statement->execute();
    $statement->store_result();
  }

  foreach ($doc['text_t'] as $text){
    if (trim($text)=="") continue;
    $statement = $mysqli->prepare("INSERT INTO texts (record_id,text_t)"
                  ." VALUES (?,?)");
    $statement->bind_param("is", $recordID,$text);
    $statement->execute();
    $statement->store_result();
  }

  foreach ($doc['publisher_location'] as $publisher_location){
    if (trim($publisher_location)=="") continue;
    $statement = $mysqli->prepare("INSERT INTO publisher_locations (record_id,publisher_location)"
                  ." VALUES (?,?)");
    $statement->bind_param("is", $recordID,$publisher_location);
  $statement->execute();
    $statement->store_result();
  }

  foreach ($doc['language'] as $language){
    if (trim($language)=="") continue;
    $statement = $mysqli->prepare("INSERT INTO languages (record_id,language)"
                  ." VALUES (?,?)");
    $statement->bind_param("is", $recordID,$language);
  $statement->execute();
    $statement->store_result();
  }
  foreach ($doc['publisher'] as $publisher){
    if (trim($publisher)=="") continue;
    $statement = $mysqli->prepare("INSERT INTO publishers (record_id,publisher)"
                  ." VALUES (?,?)");
    $statement->bind_param("is", $recordID,$publisher);
  $statement->execute();
    $statement->store_result();
  }
  
  /*$startYear = $doc['years'][0];
  $endYear = end($doc['years']);
  $statement = $mysqli->prepare("INSERT INTO years (record_id,start_year,end_year)"
                  ." VALUES (?,?,?)");
    $statement->bind_param("iii", $recordID,$startYear,$endYear);
  $statement->execute();
    $statement->store_result();*/

return $recordID;

    }

function insertIntoSubTable($table,$values,$recordID,$columnName){
  $query = "INSERT INTO $table (record_id,$columnName) VALUES (?,?)";
  foreach ($values as $value){
    if (trim($value)=="") continue; //skip if empty
    $statement = $mysqli->prepare($query);
    $statement->bind_param("is", $recordID,$value);
    $statement->execute();
    $statement->store_result();
  }
}

function getNewCallNumber($collection){
  global $ROOTDIR;
  $filename = $ROOTDIR."data/cat-".$collection.".json";

  $jsonCallNumbers = file_get_contents($filename);
  $callNumbers = json_decode($jsonCallNumbers,true);
  
  sort($callNumbers);
  $newNum = 0;
  $i=1; 

  //this is very inefficient, but it gets the job done
  //TODO: revise algorithm to make it take O(n) time instead.
  // in_array() takes O(n) so the code below probably takes O(n^2)
  // worst case scenario
  while(in_array($i,$callNumbers)) $i++;
  $newNum = $i;

  $callNumbers[] = $newNum;
  $jsonCallNumbers = json_encode($callNumbers);
  file_put_contents($filename,$jsonCallNumbers);
  return $newNum;
}

function freeCallNumber($collection,$call_number){
  global $ROOTDIR;
  $filename = $ROOTDIR."data/cat-".$collection.".json";

  $jsonCallNumbers = file_get_contents($filename);
  $callNumbers = json_decode($jsonCallNumbers,true);

  //delete from array
  if(($key = array_search($call_number, $callNumbers)) !== false) {
    unset($callNumbers[$key]);
  }

  //save file
  $jsonCallNumbers = json_encode($callNumbers);
  file_put_contents($filename,$jsonCallNumbers);
}


function addVocabularies($doc,$insertID){
    global $mysqli;
    global $contribtypes;
    global $other_heading_types;
    $contributor_headings = array_keys($contribtypes);
    $headingtypes = array_merge($contributor_headings, $other_heading_types);
    $recordID = $insertID;

    foreach ( $headingtypes as $htype) {
      if (!array_key_exists($htype,$doc)){ 
          continue;
      }
            foreach($doc[$htype] as $heading){
            $label = trim($heading[0]);
            $uri = $heading[1];
            if($htype == "subject_heading"){
                    $table = "subject_headings";
                    $labelfield = "subject_heading";
                    $crosstable = "has_subject";
                    $crossID = "subject_id";
                    #Any additional supported vocabularies will follow as else ifs here
            }
            else{
                    $table = "names";
                    $roleID = $contribtypes[$htype];
                    $labelfield = "name";
                    $crosstable = "contributors";
                    $crossID = "contributor_id";
            }

            //--updating $table to add label/uri entry if needed--
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
                    $statement->bind_result($localID, $localUri);
                    if ($statement->fetch()) {
                      if (($uri != $localUri) && (trim($uri) !="")) {
                          //check if current URI is different than uri already in table, and not null/blank
                          if(trim($localUri) !== ""){ //as long as localUri isn't null/blank, add error message
                              $newURI = "Some records had uri: ".$uri;
                              $likeURI = '"%'.$newURI.'%"';
                              $query = "UPDATE ".$table." SET problem_note = "
                                      ."CONCAT_WS('--', COALESCE(problem_note, ''), ?) WHERE id = ? "
                                      ."AND COALESCE(problem_note, '') NOT LIKE ?";
                              $params = [&$newURI, &$localID, &$likeURI];
                              $type = "sis";
                          } else { //if blank/null, update to $uri value
                              $query = "UPDATE ".$table." SET uri = ? WHERE id = ?";
                              $params = [&$uri, &$localID];
                              $type = "si";
                          }
                            $statement = $mysqli->prepare($query);
                            if (!$statement){
                                    print "Differing URI prepare statement failed";
                                    print_r( $statement->error_list) ;
                            }
                            else{
                              call_user_func_array(array($statement, "bind_param"), array_merge(array($type), $params));
                              $statement->execute();
                              $statement->store_result();
                              //$localID = $statement->insert_id;
                            }
                        }
                    }
                    else {
                      //if no matching label in table, insert label & uri
                        $statement = $mysqli->prepare("INSERT INTO ".$table." (".$labelfield.", uri) VALUES(?,?)");
                        $statement->bind_param('ss', $label, $uri);
                        $statement->execute();
                        $statement->store_result();
                        $localID = $statement->insert_id;
                    }
                }

                //--Updating $crosstable--
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


function deleteRecord($dbID){
  //free call number
  $doc = getDocFromDb($dbID);
  $call_number = $doc['call_number'];
  //print $call_number;
  //return;

  try{
preg_match('/^[\D\s]+/',$call_number,$match);
if (!isset($match[0])) throw new Exception("Notice: Undefined offset: 0 in collection");
$call_number_coll = trim($match[0]);

$call_number_num = 0;
preg_match('/[\d]+$/',$call_number,$match);
if (!isset($match[0])) throw new Exception("Notice: Undefined offset: 0 in number");
$call_number_num = trim($match[0]);
}
catch (Exception $e) {
      print 'Call number error: '.  $e->getMessage()."  On call number: ". $call_number.'<br>';
      //die();
    }

    freeCallNumber($call_number_coll,$call_number_num);



  //delete record from db
  deleteFromTable('records','id',$dbID);
  deleteFromTable('alternative_titles','record_id',$dbID);
  deleteFromTable('years','record_id',$dbID);
  deleteFromTable('notes','record_id',$dbID);
  deleteFromTable('contributors','record_id',$dbID);
  deleteFromTable('publishers','record_id',$dbID);
  deleteFromTable('publisher_locations','record_id',$dbID);
  deleteFromTable('texts','record_id',$dbID);
  deleteFromTable('languages','record_id',$dbID);
  deleteFromTable('has_subject','record_id',$dbID);

  deleteRecordFromSolr($dbID);
  //deleteFromTable('hidden_subject_headings','record_id',$dbID);
}

function deleteFromTable($table,$idColumn,$id){
  global $mysqli;
  $query = "DELETE FROM $table WHERE $idColumn=?";
  print $query."<br>";
  $statement = $mysqli->prepare($query);
  print $mysqli->error;
  $statement->bind_param("i",$id);
  $statement->execute();
  $statement->store_result();
}

//print("</br>");

//TODO: Please add error checking!!!
function parseDate($dateString){
  $parts = explode('-',$dateString);
  if (sizeof($parts)==1) {
    return array((int)$parts[0]);
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

function deleteRecordFromSolr($id){
  print 'delete_all();<br>';
  $q = 'id:'.$id;
  $data = array(
      'delete' => array(
            'query' => $q
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

  if (DEBUGGING) {
    print $queryString;
  }

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

  if (DEBUGGING) {
    print $queryString;
  }

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
    .'&hl.fl=*&facet=true&debugQuery=on';
if (DEBUGGING) {
print $queryString;
}
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
  $query = preg_replace('/".*?"(*SKIP)(*FAIL)| (AND|OR|NOT) (*SKIP)(*FAIL)| +(?!$)/', ' AND ', $query);
  $queryString = '';
  global $searchFields;
  foreach ($searchFields as $field){
    $queryString = $queryString.$field.':('.urlencode($query);
    if($field == "exact_words"){
        $queryString = $queryString.')^6%0A';
    }
    else if ($field =="title"){
      $queryString = $queryString.')^4%0A';
    }
    else if ($field =="composer"){
      $queryString = $queryString.')^3';
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
  global $ROOTDIR;
  while(file_exists($ROOTDIR.'sheet-music/'.$filePrefix.strval($counter).'.jpg')){
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



function isLoggedIn(){
  if (isset($_SESSION['logged-in'])){
    return $_SESSION['logged-in'];
  }
  else {
    $_SESSION['logged-in'] = false;
    return false;
  }
}

function isSuper(){
  return ($_SESSION['user_role']=='super') ? true : false;
}


function isInCart($id){
  $items = json_decode(file_get_contents("data/shoppingCart.json"),true);
  if (in_array($id,$items)){
    return true;
  }
  else return false;
}

function addToCart($id){
  $items = json_decode(file_get_contents("data/shoppingCart.json"),true);
  $items[] = $id;
  $jsonItems = json_encode($items);
  //print $jsonItems;
  file_put_contents("data/shoppingCart.json",$jsonItems);
}

function removeFromCart($id){
  $items = json_decode(file_get_contents("data/shoppingCart.json"),true);
  if(($key = array_search($id, $items)) !== false) {
    unset($items[$key]);
  }
  $jsonItems = json_encode($items);
  file_put_contents("data/shoppingCart.json",$jsonItems);
}


function zipForCDM($files) {
    $zip = new ZipArchive;
    global $ROOTDIR;
    $filename = $ROOTDIR."data/smdb_cdmExport.zip";
    $opened = $zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    if ($opened !== TRUE) {
        die("Unable to open zip file for CDM export");
    }
    forEach (array_keys($files) AS $id) {
        $cdmrecord = $files[$id];
        $fd = fopen('php://temp/maxmemory:1048576', 'w');
        if ($fd == false) {
            die("Failed to create temporary file");
        }
        $headers = array_keys($cdmrecord);
        fputcsv($fd, $headers, "\t");
        $rlen = count($cdmrecord[$headers[0]]);
        for ($i = 0; $i < $rlen; $i++) {
            $line = array();
            forEach ($headers AS $h) {
                array_push($line, $cdmrecord[$h][$i]);
            }
            fputcsv($fd, $line, "\t");
        }
        rewind($fd);
        $zip->addfromString($id . "/" . $id . '.txt', stream_get_contents($fd));
        fclose($fd);
    }
//var_dump($zip);
    header('Content-Type: application/zip');
    header('Content-disposition: attachment; filename=');
    header('Content-Length: ' . filesize($filename));
    readfile($filename);
    unlink($filename);
}

function getDocFromDb($recordID){
  global $mysqli;
  $statement = $mysqli->prepare("SELECT id,title,call_number,series,larger_work,collection_source,donor,scanning_technician,media_cataloguer_id,reviewer_id, admin_notes, date_created,start_year,end_year FROM records WHERE id=? LIMIT 1");
  $statement->bind_param("i",$recordID);
  $statement->execute();
  $statement->store_result();
  $statement->bind_result($id, $title, $call_number, $series, $larger_work, 	$collection_source, $donor, $scanning_technician, $media_cataloguer_id, $reviewer_id, $admin_notes,$date_created,$start_year,$end_year);
  if(!$statement->fetch()){
    return false;
  }

  $statement = $mysqli->prepare("SELECT contributor_id,role_id FROM contributors WHERE record_id=?");
  $statement->bind_param("i",$recordID);
  $statement->execute();
  $statement->store_result();
  $statement->bind_result($contributorId, $roleId);

global $contribtypes;
  $contributors = array();
  while ($statement->fetch()){
    $statement2 = $mysqli->prepare("SELECT name FROM names WHERE id=? LIMIT 1");
    $statement2->bind_param("i",$contributorId);
    $statement2->execute();
    $statement2->store_result();
    $statement2->bind_result($cName);
    if ($statement2->fetch()){
      $contributors[] = array(array_search($roleId,$contribtypes),$cName);
    }
  }

  $statement = $mysqli->prepare("SELECT subject_id FROM has_subject WHERE record_id=?");
  $statement->bind_param("i",$recordID);
  $statement->execute();
  $statement->store_result();
  $statement->bind_result($subjectId);

  $headings = array();
  while ($statement->fetch()){
    $statement2 = $mysqli->prepare("SELECT subject_heading FROM subject_headings WHERE id=? LIMIT 1");
    $statement2->bind_param("i",$subjectId);
    $statement2->execute();
    $statement2->store_result();
    $statement2->bind_result($cName);
    if ($statement2->fetch()){
      $headings[] = $cName;
    }
  }


  //var_dump($admin_notes);

  $fields = array(
    'alternative_title'=> ['alternative_titles','alternative_title'],
    'notes'=>['notes','note'],
    'text_t'=>['texts','text_t'],
    'publisher_location'=>['publisher_locations','publisher_location'],
    'publisher'=>['publishers','publisher'],
    'language'=>['languages','language']
  );

  $displayArray = array();

  foreach($fields as $field=>$values){
    //var_dump($values);
    $query = "SELECT $values[1] FROM $values[0] WHERE record_id=?";
    $statement = $mysqli->prepare($query);
    $statement->bind_param("i",$id);
    $statement->execute();
    $statement->store_result();
    $statement->bind_result($value);
    while ($statement->fetch()){
      if (trim($value)=="") continue;
      $displayArray[$field][] = $value; 
    }
  }

  $statement = $mysqli->prepare("SELECT name FROM users WHERE id=? LIMIT 1");
  $statement->bind_param("i",$media_cataloguer_id);
  $statement->execute();
  $statement->store_result();
  $statement->bind_result($media_cataloguer);
  $statement = $mysqli->prepare("SELECT name FROM users WHERE id=? LIMIT 1");
  $statement->bind_param("i",$reviewer_id);
  $statement->execute();
  $statement->store_result();
  $statement->bind_result($reviewer);
  
  $docID = (int)$id;
  $document = array(
    "id"=>$docID,
    "title"=>$title,
    "call_number"=>$call_number,
    "series"=>$series,
    "larger_work"=>$larger_work,
    "collection_source"=>$collection_source,
    "donor"=>$donor,
    "scanning_technician"=>$scanning_technician,
    "media_cataloguer"=>$media_cataloguer,
    "reviewer"=>$reviewer,
    "admin_notes"=>$admin_notes,
    "date_created"=>$date_created,
    "start_year"=>$start_year,
    "end_year"=>$end_year,

    "contributors"=>$contributors,
    "subject_heading"=>$headings
  );

  foreach ($displayArray as $key=>$value){
    $document[$key] = $value;
  }
  return $document;
}

function export_for_CDM($recordID_array,$digitalcollection,$digispec,$contributing_inst,$website) {
    //var_dump(implode($recordID_array,","));
    /*
     * Export selected records for USC CONTENTdm instance in accordance with USC DigiCol schema
     */
    $cdmbatch = array();
    global $mysqli;
    global $ROOTDIR;
    $row = array();

    foreach ($recordID_array as $recordID){
      $doc = getDocFromDb($recordID);
      if ($doc === false) continue;

            $dd = new DateTime($doc["date_created"]); #Dates digital, etc. are based on the record creation date at the moment
            $imagepaths = getImagesForId($recordID);
            $imagecount = count($imagepaths);
            
            $dd_ym = date_format($dd, 'Y-m');
            $rightsyear = date_format($dd, 'Y');
            /*$digitalcollection = "TEST COLLECTION";
            $digispec = "TEST DIGITIZATION SPEC";
            $contributing_inst = "University of South Carolina. Music Library";
            $website = NULL;*/
            $concatFields = function($fieldArray){
              $cat = "";
              foreach ($fieldArray as $value){
                $cat = $cat.$value.";";
              }
              return $cat;
            };
            $cdmrecord = array(
                "Title" => array($doc['title']),
                "Creator" => array(),
                "Contributor 1" => array(),
                "Contributor 2" => array(),
                "Contributor 3" => array(),
                "Contributor 4" => array(),
                "Donor" => array($doc["donor"]),
                "Date" => array(),
                "Approximate Date" => array(),
                "Source" => array($doc["collection_source"]),
                "Publisher" => array($concatFields($doc["publisher"])),
                "Subject" => array($concatFields($doc["subject_heading"])),
                "Digital Collection" => array($digitalcollection),
                "Web Site" => array($website),
                "Note" => array(preg_replace('/\.\./','.',preg_replace('/;/','. ',$concatFields($doc["notes"])))),
                "Contributing Institution" => array($contributing_inst),
                "Rights" => array(), #year from date digital
                "Type" => array("still image; text"),
                "Format" => array("image/jpeg"),
                "Media Type" => array("Sheet music"),
                "Identifier" => array(""),
                "Language" => array($concatFields($doc["language"])),
                "Digitization Specifications" => array($digispec), #supplied by user
                "Date Digitized" => array($dd_ym),
                "Scanning Technician" => array($doc["scanning_technician"]),
                "Metadata Cataloger" => array($doc["media_cataloguer"]),
                "Filename" => array("")
            );

            
            $filename = $ROOTDIR.$imagepaths[0];
            if (file_exists($filename)) {
                $dd = date('c', filemtime($filename));
                $dd_ym = date_format(new DateTime($dd), 'Y-m');
                $rigtsyear = date_format(new DateTime($dd), 'Y');
                $cdmrecord["Rights"] = array('Digital Copyright ' .$rightsyear. ', The University of South Carolina. All rights reserved. For more information contact the Music Library, 813 Assembly Street, Room 208, University of South Carolina, Columbia, SC 29208');
            }
            else{
                    $cdmrecord["Rights"] = array(NULL);
            }
            


            //Determines creator & contributors 1-4, creates & appends parenthetical qualifier for role(s)
            
            //concat names and roles into one string
            $nameString = "";
            foreach ($doc['contributors'] as $Rname){
              $nameString = $nameString.$Rname[1]." : ".$Rname[0]." ; ";
            }


            $possible_creators = array("composer", "lyricist", "arranger of music");
            $contributors = array();
            $creator = NULL;
            $creator_relators = array();
            $notenames = array();
            $nameswithroles = $nameString;//$row["name"];
            $nr_array = array_filter(explode(' ; ', $nameswithroles));
            
            foreach ($possible_creators as $pc) {
                foreach ($nr_array as $nr) {
                    $nar = explode(" : ", $nr);
                    if ($nar[1] == $pc) {
                        if ($creator == NULL) {
                            //if this is the first creator candidate found, set creator
                            $creator = $nar[0];
                            array_push($creator_relators, $nar[1]);
                        } elseif ($nar[0] == $creator) {
                            //if this person is already creator, add the relator term
                            array_push($creator_relators, $nar[1]);
                        } elseif (in_array($nar[0], array_keys($contributors)) == False) {
                            //if also not in contributors, check if we've filled the contributors slots
                            if (count($contributors) < 4) {
                                $contributors[$nar[0]] = array("relators" => array($nar[1]), "contribno" => count($contributors) + 1);
                            } else {
                                if (in_array($nar[0], array_keys($notenames))) {
                                    array_push($notenames[$nar[0]], $nar[1]);
                                } else {
                                    $notenames[$nar[0]] = array($nar[1]);
                                }
                            }
                        } else {  //$nar[0] is in $contributors          
                            array_push($contributors[$nar[0]]["relators"], $nar[1]);
                        }
                    } elseif(in_array($nar[1], $possible_creators)){
                        
                    }else{
                        if (in_array($nar[0], array_keys($notenames))) {
                            if(in_array($nar[1], $notenames[$nar[0]])){
                            }else{
                            array_push($notenames[$nar[0]], $nar[1]);
                            }
                        } else {
                                    $notenames[$nar[0]] = array($nar[1]);
                    }}
                    $cdmrecord["Creator"] = array($creator . " (" . join(', ', $creator_relators) . ")");
                    forEach (array_keys($contributors) as $contributor) {
                        $name = $contributor;
                        $num = $contributors[$contributor]["contribno"];
                        $relators = "(" . join(", ", $contributors[$contributor]["relators"]) . ")";
                        $cdmrecord["Contributor " . (string) $num] = array($name . " " . $relators);
                    }
                }
            }

           forEach(array_keys($notenames) as $nn){
               $roles = join("and", $notenames[$nn] );
                    $cdmrecord["Note"][0] = join('. ', array($cdmrecord["Note"][0], ucfirst($roles).": ". $nn));
           }
            //Determine if single known date or approximate date range, format circa dates
            if ($doc['start_year'] == $doc['end_year']) {
                array_push($cdmrecord["Date"], $doc['start_year']);
                array_push($cdmrecord["Approximate Date"], NULL);
            } else {
                array_push($cdmrecord["Date"], NULL);
                array_push($cdmrecord["Approximate Date"], "circa " . $doc["start_year"] . "-" . $doc["end_year"]);
            }

            //array_push($cdmrecord["Alternative Title"], $row["alttitle"]);
            
            //Fill record array for tsv
            $id_root = "smdb" . $doc["id"];
            if ($imagecount != 0) {
                for ($i = 0; $i < $imagecount; $i++) {
                    forEach (array_keys($cdmrecord) AS $fld) {
                        if ($fld == "Title") {
                            array_push($cdmrecord["Title"], "Page " . strval($i + 1));
                        } elseif ($fld == "Digital Collection") {
                            array_push($cdmrecord["Digital Collection"], $digitalcollection);
                        } elseif ($fld == "Identifier") {
                            array_push($cdmrecord["Identifier"], $id_root . "_" . strval($i + 1)); 
                        } elseif ($fld == "Filename") {
                            $fn = $id_root . "_" . strval($i + 1) . ".jpg";
                            if (in_array($fn, $cdmrecord["Filename"])) {
                                
                            } else {
                                array_push($cdmrecord["Filename"], $fn);
                            }
                        } elseif ($fld == "Contributing Institution") {
                            array_push($cdmrecord["Contributing Institution"], $contributing_inst);
                        } else {
                            if ($cdmrecord[$fld] === array()) {
                                array_push($cdmrecord[$fld], NULL);
                            }
                            array_push($cdmrecord[$fld], NULL);
                        }
                    }
                }
            } else {
                trigger_error("Record " . $doc["id"] . " has not been digitized");
            }
            $cdmbatch [$id_root] = $cdmrecord;

    }
    //print '<pre>';
    //var_dump($cdmbatch);
    //print '</pre>';        
//die();
    zipForCDM($cdmbatch);
}



?>
