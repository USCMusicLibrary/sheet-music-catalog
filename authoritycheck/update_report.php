<?php

require "../header.php";
require_once('../db-config.php');
function fetch_problems(){
     global $mysqli;
     $query = "SELECT id, name, uri, nameUpdate, problem_note FROM names WHERE nameUpdate IS NOT NULL";
     $result = $mysqli -> query($query);
     $count = $result->num_rows;
     print '<h1>Name updates ('.$count.')</h1>';
     print '<table><tr style="font-size:125%"><td></td><td>ID</td><td>Current heading</td><td>New heading</td><td>URI</td><td>Problem</td></tr>';
     $i = 0;
     while($row = $result->fetch_array(MYSQLI_BOTH)){
         if(trim($row['nameUpdate']) != ""){
             $ischecked = " checked";
         } else {
             $ischecked = '';
         }
         print "<tr><td><input type='checkbox' name='cb".$i."'".$ischecked.">&nbsp;</input></td><td>".$row['id']."</td><td>".$row['name']."</td><td>".$row["nameUpdate"]
                 ."</td><td><a href='".$row['uri']."'>".$row['uri'].'</a></td><td>'.$row['problem_note']."</td></tr>";
         $i++;
}
print '</table>';
  }


?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">

  <div class="row page-header">
    <div class="col-xs-12">
      <?php fetch_problems(); ?>
    </div>
  </div>
  </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>