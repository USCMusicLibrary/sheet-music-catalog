<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
//submission
session_start();

if (!$_SESSION['logged-in']){
    header("Location: login");
    die();
}

require "../header.php";
require "admin-navigation.php";



require_once "../functions.php";

if (!isLoggedIn() || !isSuper() ){
  print "<h1 class=\"text-danger\">Unauthorized</h1>";
  die();
}

require_once "../db-config.php";


if(isset($_POST['selected_names']) || isset($_POST['selected_headings'])):
  foreach($_POST['selected_names'] as $nameUri){
    $statement = $mysqli->prepare("SELECT nameUpdate FROM names WHERE uri=? LIMIT 1");
    $statement->bind_param("s",$nameUri);
    $statement->execute();
    $statement->store_result();
    $statement->bind_result($nameUpdate);
    if($statement->fetch()){
      $statement = $mysqli->prepare("UPDATE names SET name=?,nameUpdate='',problem_note='' WHERE uri=?");
      $statement->bind_param("ss",$nameUpdate,$nameUri);
      $statement->execute();
      $statement->store_result();
    }
  }
  foreach($_POST['selected_headings'] as $nameUri){
    $statement = $mysqli->prepare("SELECT subjectUpdate FROM subject_headings WHERE uri=? LIMIT 1");
    $statement->bind_param("s",$nameUri);
    $statement->execute();
    $statement->store_result();
    $statement->bind_result($nameUpdate);
    if($statement->fetch()){
      $statement = $mysqli->prepare("UPDATE subject_headings SET subject_heading=?,subjectUpdate='',problem_note='' WHERE uri=?");
      $statement->bind_param("ss",$nameUpdate,$nameUri);
      $statement->execute();
      $statement->store_result();
    }
  }

else:
?>
<div class="container-fluid">
  <div class="row">
    <div class="col-xs-8 col-xs-offset-2">

    <form action="authorityCheck" method="POST">
      <table>
        <tr><th>Type</th><th>URI</th><th>Value</th><th>Update</th><th>Problem</th><th>Select</th></tr>
        <?php
          $statement = $mysqli->prepare("SELECT uri,name,nameUpdate,problem_note FROM names WHERE nameUpdate!=''");
          $statement->execute();
          $statement->store_result();
          $statement->bind_result($uri,$name,$nameUpdate,$problem_note);
          while($statement->fetch()):
        ?>
        <tr><th>Name</th>
        <th><?php print $uri;?></th>
        <th><?php print $name;?></th>
        <th><?php print $nameUpdate;?></th>
        <th><?php print $problem_note;?></th>
        <th><input type="checkbox" name="selected_names[]" value="<?php print $uri;?>" checked></th></tr>
        <?php endwhile;$statement = $mysqli->prepare("SELECT uri,subject_heading,subjectUpdate,problem_note FROM subject_headings WHERE subjectUpdate!=''");
          $statement->execute();
          $statement->store_result();
          $statement->bind_result($uri,$name,$nameUpdate,$problem_note);
          while($statement->fetch()):
        ?>
        <tr><th>Heading</th>
        <th><?php print $uri;?></th>
        <th><?php print $name;?></th>
        <th><?php print $nameUpdate;?></th>
        <th><?php print $problem_note;?></th>
        <th><input type="checkbox" name="selected_headings[]" value="<?php print $uri;?>" checked></th></tr>
        <?php endwhile;?>
      </table>
    </form>
    </div>
  </div>
</div> <!-- container-fluid -->
<?php 
endif;//if(isset($_POST['selected_names']) || isset($_POST['selected_headings'])):


require "../footer.php";

//require "layout/scripts.php";

?>