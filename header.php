<?php
require_once "config.php";
require_once "functions.php";
error_reporting(E_ALL);
ini_set("display_errors", true);
ini_set("display_startup_errors", true);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Sheet Music Catalog - Music Library - University of South Carolina</title>

    <!-- Bootstrap core CSS -->
    <link href="<?php print $ROOTURL;?>bootstrap/css/bootstrap.min.css" rel="stylesheet">


    <link href="<?php print $ROOTURL;?>css/search.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php print $ROOTURL;?>css/font-awesome.min.css">



    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">


    <link rel="stylesheet" href="<?php print $ROOTURL;?>css/awesomplete.css" />

<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />


    <style>
    label:hover {
    background: #f2f5ff;
    border-radius:5px;
    padding:2px 4px;
}</style>

  <!--<link rel="stylesheet" href="/resources/demos/style.css">-->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <!--<link href="bootstrap/css/ie10-viewport-bug-workaround.css" rel="stylesheet">-->


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="http://library.sc.edu/p/Music/Sheet_Music"><img style="height: 1em;" src="<?php print $ROOTURL; ?>img/banner-image-white.png"></a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="<?php print $ROOTURL;?>index">Search</a></li> 
            <li><a href="<?php print $ROOTURL;?>browse">Browse</a></li>
            <?php if (isLoggedIn()):?><li><a href="<?php print $ROOTURL;?>admin/logout">Log out</a></li><?php endif;?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

  <div class="container-fluid" id="main-container">
