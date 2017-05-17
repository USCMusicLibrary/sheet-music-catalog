<?php
//index for admin part
session_start();
require "../header.php";


require_once "../functions.php";

require_once "adminFunctions.php";

require "admin-navigation.php";

$statement = $mysqli->prepare("SELECT id, mid,title,call_number,series,larger_work,collection_source,donor,scanning_technician,media_cataloguer_id,reviewer_id,status FROM records WHERE status='pending'");
$statement->execute();
$statement->store_result();
$statement->bind_result($id, $mid, $title, $call_number, $series, $larger_work, 	$collection_source, $donor, $scanning_technician, $media_cataloguer, $reviewer, $status);




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
<th>Donor</th>
<th>Media Cataloguer</th>
<!--<th>Scanning Technician</th>
<th>Reviewer</th>-->
              <th>Status</th>
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
<th><?php print $donor;?></th>
<th><?php print getUsernameFromID($media_cataloguer);?></th>
<th><?php print $status;?></th>
<!--<th><?php print $scanning_technician;?></th>
<th><?php print $reviewer;?></th>-->
                <th><a href="edit?id=<?php print $id;?>" class="btn btn-danger">Edit</a></th>
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
