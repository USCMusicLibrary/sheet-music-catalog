<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
//view all pending records
session_start();

if (!$_SESSION['logged-in']){
    header("Location: login");
    die();
}

require "../header.php";


require_once "../functions.php";

require "admin-navigation.php";

$statement = $mysqli->prepare("SELECT id, mid,title,call_number,series,larger_work,collection_source,donor,scanning_technician,media_cataloguer_id,reviewer_id,status FROM records WHERE media_cataloguer_id=?");
$statement->bind_param("i",$_SESSION['user_id']);
$statement->execute();
$statement->store_result();
$statement->bind_result($id, $mid, $title, $call_number, $series, $larger_work, $collection_source, $donor, $scanning_technician, $media_cataloguer, $reviewer, $status);




?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Title</th>
<th>Call Number</th>
<th>Series</th>
<th>Larger Work</th>
<th>Collection Source</th>
<th>Status</th>
<!--<th>Scanning Technician</th>
<th>Media Cataloguer</th>
<th>Reviewer</th>-->
              <th></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php while ($statement->fetch()):?>
              <tr>
 <th><?php print $id;?></th>
<th><?php print $title;?></th>
<th><?php print $call_number;?></th>
<th><?php print $series;?></th>
<th><?php print $larger_work;?></th>
<th><?php print $collection_source;?></th>
<th><?php print $status;?></th>

<!--<th><?php //print $scanning_technician;?></th>
<th><?php //print $media_cataloguer;?></th>
<th><?php // print $reviewer;?></th>-->
                <th><a href="edit?id=<?php print $id;?>" class="btn btn-danger">Edit</a></th>
                <th><a href="duplicate?id=<?php print $id;?>" class="btn btn-danger">Duplicate</a></th>
              </tr>
            <?php endwhile;?>
          </tbody>
        </table>
      </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>
