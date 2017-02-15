<?php
//index for admin part
require "../header.php";


require "../functions.php";

require_once "../db-config.php";

?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">
      <pre>
      <?php //print_r ($_POST);?>
      </pre>

      <?php foreach ($_POST as $key => $val):?>
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

      $title = $_POST['title'];
      //$alt_title = $_POST['alt-title'];
      $publisher = $_POST['publisher'];
      //$publisher_location = $_POST['publisher_location'];
      $text_t = $_POST['text-t'];
      $call_number = $_POST['call-number'];
      $series = $_POST['series'];
      $larger_work = $_POST['larger-work'];
      $collection_source = $_POST['collection'];
      $donor = $_POST['donor'];
      $scanning_tech = $_POST['scanning-tech'];

      $media_cataloguer = "CURRENTUSER";

      $statement = $mysqli->prepare("INSERT INTO records (title,publisher,call_number,series,larger_work,collection_source,donor,scanning_technician,media_cataloguer,status,date_created,date_modified)"
                  ." VALUES (?,?,?,?,?,?,?,?,?,'pending',NOW(),NOW())");
      $statement->bind_param("sssssssss", 
              $title, 
              $publisher, 
              $call_number, 
              $series, 
              $larger_work, 
              $collection_source, 
              $donor, 
              $scanning_technician, 
              $media_cataloguer);
  $statement->execute();
  $statement->store_result();
      ?>

      </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>