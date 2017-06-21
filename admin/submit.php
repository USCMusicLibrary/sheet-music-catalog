<?php
//submission
session_start();
require "../header.php";


require_once "../functions.php";

require_once "../db-config.php";

?>
<div class="container-fluid">
  <div class="row">
   <h1><a class="btn btn-lg btn-danger" href="all">continue</a></h1>
      <div class="col-xs-8 col-xs-offset-2">
      <pre>
      <?php print_r ($_POST);?>
      </pre>
<?php 

$years = array();
$yearStart = intval($_POST['year_start']);
$yearEnd = intval($_POST['year_end']);

//TODO: check that dates make sense
/*if ($yearEnd==0) {
  $years[0]
}
else {*/
  $newYearStart = $yearStart;
  while ($yearStart<=$yearEnd){
    $years[] = $yearStart++;
  }
//}


      $document = array (
        'id' => 0,
'title' => $_POST['title'],
'alternative_title' => $_POST['alt-title'],
'publisher' => $_POST['publisher'],
'publisher_location' => $_POST['publisher_location'],
'years' =>  $years,
'years_text' => $newYearStart."-".$yearEnd,
'language' => $_POST['language'],
'text_t' => $_POST['text-t'],
'notes' => $_POST['note'],
'donor' => $_POST['donor'],
'call_number' => $_POST['call_number'],
'series' => $_POST['series'],
'collection_source' =>$_POST['collection'],
'larger_work' => $_POST['larger-work'],
'scanning_technician' => $_POST['scanning-tech'],
'admin_notes' => $_POST['msg'],
'media_cataloguer_id' => $_SESSION['user_id'],
'reviewer_id' => $_SESSION['user_id']
    );

$contributor_headings = array_keys($contribtypes);
$headingtypes = array_merge($contributor_headings, $other_heading_types);

foreach ($headingtypes as $htype){
  $document[$htype] = array();
  if (isset($_POST[$htype])){
    foreach ($_POST[$htype] as $heading){
      if (trim($heading)=="") continue;
      $document[$htype][] = array($heading,"");
    }
  }
}
/*
    'composer' => isset($_POST['composer']) ? array($_POST['composer'],'') : [] ,
'lyricist' => $_POST['lyricist'],
'arranger' => $_POST['arranger'],
'editor' => $_POST['editor'],
'photographer' => $_POST['photographer'],
'illustrator' =>$_POST['illustrator'],
'subject_heading' => [],
*/
?>
<pre>
<?php print_r ($document);?>
</pre>
<?php
    $recordID = insertDocDb($document,'pending');
    addVocabularies($document,$recordID);
      ?>

      </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>