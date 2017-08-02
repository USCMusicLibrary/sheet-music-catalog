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


$statement = $mysqli->prepare("UPDATE records SET status='approved' WHERE id=?");
$statement->bind_param("i",$_GET['id']);
$statement->execute();
$statement->store_result();


$document = getDocFromDB($_GET['id']);

$years = $document['start_year']."-".$document['end_year'];

$document['composer'] =  $document['contributors']['composer'];
$document['lyricist'] = $document['contributors']['lyricist'];
$document['arranger'] = $document['contributors']['arranger'];
$document['editor'] = $document['contributors']['editor'];
$document['photographer'] = $document['contributors']['photographer'];
$document['illustrator'] = $document['contributors']['illustrator'];
$document['years'] =  parseDate($years);
$document['years_text'] = trim($years);
$document['has_image'] = (idHasImage($document['id']))?"Online score" : "Print only";

$solrDoc = array();
    global $solrFieldNames;
    foreach ($document as $key => $val){
      if (array_key_exists($key,$solrFieldNames)){
        $solrDoc[$key] = $val;
      }
    }

indexDocument($solrDoc);

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