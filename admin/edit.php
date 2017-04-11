<?php
//edit page
session_start();
require "../header.php";


require_once "../functions.php";

$statement = $mysqli->prepare("SELECT id,title,publisher,call_number,series,larger_work,collection_source,donor,scanning_technician,media_cataloguer,reviewer FROM records WHERE id=? LIMIT 1");
$statement->bind_param("i",$_GET['id']);
$statement->execute();
$statement->store_result();
$statement->bind_result($id, $title, $publisher, $call_number, $series, $larger_work, 	$collection_source, $donor, $scanning_technician, $media_cataloguer, $reviewer);
$statement->fetch();


?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">
        <p>
          <strong>Title: </strong><br>
            <span class="text-primary"><?php print $title;?></span>
            <input class="clickedit" type="text" />
        </p>

        <p>
          <strong>Publisher: </strong><br>
            <span class="text-primary"><?php print $publisher;?></span>
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