<?php
//register page


require "../functions.php";



$dialog = "";
if (isset($_POST["username"], $_POST["password1"], $_POST["password2"], $_POST["email"])) {
  require_once "adminFunctions.php";
  $dialog = register($_POST["username"], $_POST["password1"], $_POST["password2"], $_POST["email"]);
  if (strcmp($dialog, "Success") === 0) {
    header("Location: users");
  }
}

$title = "Register";
$loginRequired = false;

require "../header.php";


?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">

  <div class="row page-header">
    <div class="col-xs-12">
      <h1>Register</h1>
      <?php if ($dialog !== ""): ?>
        <p class="lead text-danger text-center"><?php print $dialog; ?></p>
      <?php endif; ?>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-6 center-block">
      <form class="form-horizontal" action="<?php print htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
        <fieldset>
          <section class="form-group">
            <label for="username" class="col-xs-4 control-label">Username</label>
            <div class="col-xs-8">
              <input type="text" class="form-control" id="username" name="username" autofocus="" required="">
            </div>
          </section>

          <section class="form-group">
            <label for="password1" class="col-xs-4 control-label">Password</label>
            <div class="col-xs-8">
              <input type="password" class="form-control" id="password1" name="password1" required="">
            </div>
          </section>

          <section class="form-group">
            <label for="password2" class="col-xs-4 control-label">Confirm Password</label>
            <div class="col-xs-8">
              <input type="password" class="form-control" id="password2" name="password2" required="">
            </div>
          </section>

          <section class="form-group">
            <label for="email" class="col-xs-4 control-label">Email Address</label>
            <div class="col-xs-8">
              <input type="email" class="form-control" id="email" name="email" required="">
            </div>
          </section>

          <section class="form-group">
            <div class="col-xs-8 pull-right">
              <?php //remove site key until we enable captcha ?>
              <div class="g-recaptcha" data-sitekey=""></div>
            </div>
          </section>

          <section class="form-group">
            <div class="col-xs-12">
              <button type="submit" class="btn btn-primary pull-right">Register</button>
            </div>
          </section>
        </fieldset>
      </form>
    </div>
  </div>



      </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>