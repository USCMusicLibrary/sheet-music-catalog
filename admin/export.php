<?php
//edit page
session_start();
require "../header.php";


require_once "../functions.php";

require "admin-navigation.php";

?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">
        <h1 class="text-primary">Records to export</h1>
        <?php
          $recordArray = json_decode(file_get_contents($ROOTURL."data/shoppingCart.json"),true);
          $exportArray = array();
          foreach ($recordArray as $rec) $exportArray[] = $rec;

          foreach ($exportArray as $recordID):
          $doc = getDocFromDb($recordID);?>
          <p><strong>Record id: <?php print $recordID;?></strong> - <em><?php print $doc['title'];?></em></p>
          <?php
          endforeach;
        ?>

        <form action="exportZip" method="GET">
        <input type="hidden" name="action" value="export">
        <input type="submit" value="Export" class="form-control btn btn-large btn-danger">
        </form>
      </div>
  </div>
</div> <!-- container-fluid -->
<?php 


require "../footer.php";

//require "layout/scripts.php";

?>