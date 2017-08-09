<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
//edit page
session_start();
require "../header.php";


require_once "../functions.php";

require "admin-navigation.php";


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
    $displayArray[$solrFieldNames[$field]['field_title']][] = $value; 
  }
}
 //var_dump($displayArray);
?>
<div class="container-fluid">
  <div class="row">
    <?php if (isSuper()):?>
      <div class="col-xs-3 col-xs-offset-9">
        <a href="approve?id=<?php print $id;?>" class="btn btn-success">Approve</a>
      </div>
    <?php endif;?>
      <div class="col-xs-8 col-xs-offset-2">
        <p>
          <strong>Title: </strong><br>
            <span class="text-primary"><?php print $title;?></span>
            <input class="clickedit" type="text" />
        </p>

        <p>
          <strong>Call Number: </strong><br>
            <span class="text-primary"><?php print $call_number;?></span>
            <input class="clickedit" type="text" />
        </p>

        <p>
          <strong>Series: </strong><br>
            <span class="text-primary"><?php print $series;?></span>
            <input class="clickedit" type="text" />
        </p>

        <p>
          <strong>Larger work: </strong><br>
            <span class="text-primary"><?php print $larger_work;?></span>
            <input class="clickedit" type="text" />
        </p>

        <p>
          <strong>Collection source: </strong><br>
            <span class="text-primary"><?php print $collection_source;?></span>
            <input class="clickedit" type="text" />
        </p>

        <p>
          <strong>Donor: </strong><br>
            <span class="text-primary"><?php print $donor;?></span>
            <input class="clickedit" type="text" />
        </p>

        <p>
          <strong>Scanning technician: </strong><br>
            <span class="text-primary"><?php print $scanning_technician;?></span>
            <input class="clickedit" type="text" />
        </p>

        <p>
          <strong>Media Cataloguer: </strong><br>
            <span class="text-primary"><?php print $media_cataloguer;?></span>
            <input class="clickedit" type="text" />
        </p>

        <p>
          <strong>Reviewer: </strong><br>
            <span class="text-primary"><?php print $reviewer;?></span>
            <input class="clickedit" type="text" />
        </p>

        <?php foreach ($displayArray as $key=>$values):?>
        <p>
          <strong><?php print $key;?>: </strong>
            <?php foreach ($values as $val): ?>
            <br>
            <span class="text-primary"><?php print $val;?></span>
            <input class="clickedit" type="text" />
            <?php endforeach;?>
        </p>
        <?php endforeach;?>
      </div>
      <div class="col-xs-8 col-xs-offset-2">
        <a href="delete?id=<?php print $id; ?>" class="btn btn-lg btn-danger">Delete record</a>
      </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>