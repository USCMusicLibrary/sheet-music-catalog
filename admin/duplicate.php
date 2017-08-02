<?php
//edit page
session_start();

if (!$_SESSION['logged-in']){
    header("Location: login");
    die();
}

require "../header.php";


require_once "../functions.php";

require "admin-navigation.php";


$statement = $mysqli->prepare("SELECT id,title,call_number,series,larger_work,collection_source,donor,scanning_technician,media_cataloguer_id,reviewer_id,admin_notes FROM records WHERE id=? LIMIT 1");
$statement->bind_param("i",$_GET['id']);
$statement->execute();
$statement->store_result();
$statement->bind_result($id, $title, $call_number, $series, $larger_work, 	$collection_source, $donor, $scanning_technician, $media_cataloguer, $reviewer,$adminNotes);
$statement->fetch();

$statement2 = $mysqli->prepare("INSERT INTO records (title,call_number,series,larger_work,collection_source,donor,scanning_technician,media_cataloguer_id,reviewer_id,admin_notes,status) VALUES (?,?,?,?,?,?,?,?,?,?,'pending')");
$statement2->bind_param("ssssssssss",$title, $call_number, $series, $larger_work, 	$collection_source, $donor, $scanning_technician, $media_cataloguer, $reviewer,$adminNotes);
$statement2->execute();
$statement2->store_result();

$insertId = $mysqli->insert_id;

$statement = $mysqli->prepare("SELECT contributor_id,role_id FROM contributors WHERE record_id=?");
$statement->bind_param("i",$_GET['id']);
$statement->execute();
$statement->store_result();
$statement->bind_result($contributorId, $roleId);

$contributors = array();
while ($statement->fetch()){
  $statement2 = $mysqli->prepare("INSERT INTO contributors (contributor_id,role_id,record_id) VALUES (?,?,?)");
  $statement2->bind_param("iii",$contributorId,$roleId,$insertId);
  $statement2->execute();
  $statement2->store_result();
}

$statement = $mysqli->prepare("SELECT subject_id FROM has_subject WHERE record_id=?");
$statement->bind_param("i",$_GET['id']);
$statement->execute();
$statement->store_result();
$statement->bind_result($subjectId);

$headings = array();
while ($statement->fetch()){
  $statement2 = $mysqli->prepare("INSERT INTO has_subject (subject_id,record_id) VALUES (?,?)");
  $statement2->bind_param("ii",$subjectId,$insertId);
  $statement2->execute();
  $statement2->store_result();
}


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
    //if (trim($value)=="") continue;
    $query = "INSERT INTO $values[0] ($values[1],record_id) VALUES (?,?)";
    $statement2 = $mysqli->prepare($query);
        print $value."<br>".$query."<br>";

    $statement2->bind_param("si",$value,$insertId);
    $statement2->execute();
    $statement2->store_result();
  }
}

$statement = $mysqli->prepare("SELECT start_year, end_year FROM years WHERE record_id=? LIMIT 1");
$statement->bind_param("i",$_GET['id']);
$statement->execute();
$statement->store_result();
$statement->bind_result($startYear,$endYear);
$statement->fetch();

$statement2 = $mysqli->prepare("INSERT INTO years (start_year,end_year,record_id) VALUES (?,?,?)");
    $statement2->bind_param("iii",$startYear,$endYear,$insertId);
    $statement2->execute();
    $statement2->store_result();

 //var_dump($displayArray);
?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">
        <h1 class="text-success">Record duplicated</h1>
      </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>