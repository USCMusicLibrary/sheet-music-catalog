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

      <?php foreach ($_POST as $key => $val):
      continue;?>
        <p>
          <strong><?php print $key;?>: </strong>
          <?php 
            if (is_array($val)){
                foreach ($val as $value){
                    if (trim($value)=="") continue;
                    print '<br>'.$value;
                }
            }
            else print $val;
          ?>
        </p>
      <?php endforeach;

      $document = array (
        'id' => 0,
'title' => $_POST['title'],
'alternative_title' => $_POST['alt-title'],
'composer' => $_POST['composer'],
//'composer_uri' => $fields[4],
'lyricist' => $_POST['lyricist'],
//'lyricist_uri' => $fields[6],
'arranger' => $_POST['arranger'],
//'arranger_uri' => $fields[8],
'editor' => $_POST['editor'],
'photographer' => $_POST['photographer'],
'illustrator' =>$_POST['illustrator'],
'publisher' => $_POST['publisher'],
'publisher_location' => $_POST['publisher_location'],
'years' =>  [1234],
'years_text' => "1234",
'language' => $_POST['language'],
'text_t' => $_POST['text-t'],
'notes' => $_POST['note'],
'donor' => $_POST['donor'],
//'Distributor' => $fields[19],
//'subject_heading' =>  array_filter(explode( '|',trim($fields[20]))),
'subject_heading' => [],
'call_number' => $_POST['call_number'],
//'PlateNumber' => $fields[23],
'series' => $_POST['series'],
'collection_source' =>$_POST['collection'],
'larger_work' => $_POST['larger-work']
//'has_image' => (idHasImage($fields[1]))?"Online score" : "Print only"
//'Keywords' => $fields[27],
//'Original_Notes' => $fields[28],
//'zImagePath' => $fields[29],
//'MidiPath' => $fields[30],
//'zNumberOfPages' => $fields[31],
//'TitleSearchHits' => $fields[32],
//'Incomplete' => $fields[33]
    );

    insertDocDb($document,'pending');
      ?>

      </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>