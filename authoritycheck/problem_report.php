<?php

require "../header.php";
require_once('../db-config.php');
function fetch_problems(){
     global $mysqli;
     //print $mysqli->character_set_name();
     $query = "SELECT id, name, uri, nameUpdate, problem_note FROM names WHERE problem_note IS NOT NULL";
     $result = $mysqli -> query($query);
     $count = $result->num_rows;
     print '<h1>Problem headings--Names ('.$count.')</h1>';
     print '<table><tr style="font-size:125%"><td>ID</td><td>Current heading</td><td>New heading</td><td>URI</td><td>Problem</td></tr>';
     $i = 0;
     while($row = $result->fetch_array(MYSQLI_BOTH)){
         print "<tr><td>".$row['id']."</td><td>".$row['name']."</td><td>".$row["nameUpdate"]
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