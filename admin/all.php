<?php
//index for admin part
require "../header.php";


require "../functions.php";

$path = $ROOTURL.'data/db2.json';
$dataJson = file_get_contents($path);
//if ($dataJson===FALSE) echo"error";
$data = json_decode($dataJson,true);
//print json_last_error();
//var_dump($data);

?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Title</th>
              <th>Composer</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($data as $item):?>
              <tr>
                <th><?php print $item['id'];?></th>
                <th><?php print $item['title'];?></th>
                <th><?php print $item['composer'][0];?></th>
                <th><a href="edit?id=<?php print $item['id'];?>" class="btn btn-danger">Edit</a></th>
              </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>