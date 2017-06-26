<?php
//forgot password

session_start();
require "../header.php";


require_once "../functions.php";

require_once "adminFunctions.php";

if (isset($_GET['user'])){
  $user = $_GET['user'];
}
else {
  $user = NULL;
}

//require "admin-navigation.php";

?>
<div class="container-fluid">
  <div class="row">
    <div class="col-xs-8 col-xs-offset-2">
    <?php if ($user==NULL):?>
      <form action="forgotPassword" method="GET">
      <label for="user">Username: </label><input class="form-control" name="user" id="user">
      <input type="submit" class="btn-danger btn" value="Submit"> 
    <?php else:
      if (sendPasswordReset($user)):?>
      <h1 clas="text-success">Recovery email sent. Please check your spam folder if you do not receive it within a few minutes.</h1>
      <?php else:?>
        <h1 clas="text-success">Unable to send recovery email.</h1>
      <?php endif;?>
    <?php endif;?>
    </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>
