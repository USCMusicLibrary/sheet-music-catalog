<?php
/**
 */
//TODO: add some error checking

$title = "Users";
session_start();

require "../db-config.php";
require_once "../functions.php";
require "../header.php";


require "admin-navigation.php";

if (!isLoggedIn() || !isSuper() ){
  print "<h1 class=\"text-danger\">Unauthorized</h1>";
  die();
}
?>

<div class="container">
  <div class="row page-header">

  </div>
  <div class="row">
<!-- debug -->
  <div>
    
  </div>
      <div class="col-xs-6 center-block">
        <table class="table table-striped table-hover">
        <tr><th>User</th><th>Email</th><th>Role</th></tr>
        <?php
          global $mysqli;
          $statement = $mysqli->prepare("SELECT username,email,user_role FROM users");
          $statement->execute();
          $statement->store_result();
          $statement->bind_result($user,$email,$role);

          while($statement->fetch()):?>
            <tr><td><?php print $user;?></td><td><?php print $email;?></td><td><?php print $role;?></td></tr>
          <?php endwhile;
        ?>
      </div>
  </div>
</div>

<?php require "../footer.php"; ?>
