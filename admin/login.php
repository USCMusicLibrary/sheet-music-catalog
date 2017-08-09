<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
/**
 */
session_start();



$dialog = isset($_GET["dialog"]) ? $_GET["dialog"] : "";

if (isset($_POST)) {
  require_once "adminFunctions.php";
}

if (isset($_POST["username"], $_POST["password"])) {
  $dialog = login($_POST["username"], $_POST["password"]);

  if ($dialog == "Success") {
    $_SESSION['logged-in']=true;
    $_SESSION['user']=$_POST['username'];
    header("Location: index");
    die();
  }
}

if (isset($_POST["username"]) && !isset($_POST["password"])) {
  $dialog = sendPasswordReset($_POST["username"]);
}

if (isset($_POST["email"])) {
  $dialog = sendUsername($_POST["email"]);
}

//TODO: add some error checking

$title = "Login";
$loginRequired = false;
require "../header.php";
?>

<div class="container">
  <div class="row page-header">
    <div class="col-xs-12">
      <h1>Login</h1>
      <?php if ($dialog !== ""): ?>
        <p class="lead text-danger text-center"><?php print $dialog; ?></p>
      <?php endif; ?>

    </div>
  </div>

  <div class="row">
      <div class="col-xs-6 center-block">
        <form class="form-horizontal" action="login" method="POST">
          <fieldset>
            <section class="form-group">
              <label for="username" class="col-xs-2 control-label">Username</label>
              <div class="col-xs-10">
                <input type="text" class="form-control" id="username" name="username" autofocus="">
              </div>
            </section>

            <section class="form-group">
              <label for="password" class="col-xs-2 control-label">Password</label>
              <div class="col-xs-10">
                <input type="password" class="form-control" id="password" name="password">
              </div>
            </section>

            <section class="form-group">
              <div class="col-xs-12">
                <a href="forgotPassword" class="pull-left" style="display: block; margin-top: 10px;">Forget your username or password?</a>
                <button type="submit" class="btn btn-primary pull-right">Login</button>
              </div>
            </section>

        </fieldset>
      </form>

    </div>

    <div>
    <h2 class="text-primary"><a href="register">Click here to register</a></h2>
  </div>

  </div>
</div>

<?php require "../footer.php"; ?>
