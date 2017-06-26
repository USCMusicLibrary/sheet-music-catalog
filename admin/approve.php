<?php
//edit page
session_start();
require "../header.php";


require_once "../functions.php";

require "admin-navigation.php";


$statement = $mysqli->prepare("UPDATE records SET status='approved' WHERE id=?");
$statement->bind_param("i",$_GET['id']);
$statement->execute();
$statement->store_result();

$statement = $mysqli->prepare("SELECT id,title,call_number,series,larger_work,collection_source,donor,scanning_technician,media_cataloguer_id,reviewer_id FROM records WHERE id=? LIMIT 1");
$statement->bind_param("i",$_GET['id']);
$statement->execute();
$statement->store_result();
$statement->bind_result($id, $title, $call_number, $series, $larger_work, 	$collection_source, $donor, $scanning_technician, $media_cataloguer, $reviewer);
$statement->fetch();

$fields = array(
  'alternative_title'=> ['alternative_titles','alternative_title'],
  'notes'=>['notes','note'],
  'text_t'=>['texts','text_t'],
  'publisher_location'=>['publisher_locations','publisher_location'],
  'publisher'=>['publishers','publisher'],
  'language'=>['languages','language']
);

$multiFieldArray = array();

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
    $multiFieldArray[$field][] = $value; 
  }
}


$solrDocument = $document = array (
  'id' => $id,
  'title' => $title,
  'alternative_title' => $multiFieldArray['alternative_title'],
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



?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-12">
        <h1 class="text-danger">Record <?php print $_GET['id']; ?> approved</h1>
      </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>