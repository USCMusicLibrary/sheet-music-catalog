<?php

require_once "functions.php";

if (!isset($_GET['id']) || !isset($_GET['a'])){
  die();
}
$action = $_GET['a'];
if ($action=="rm"){
  removeFromCart($_GET['id']);
}
else if ($action=="add"){
  addToCart($_GET['id']);
}

header("Location: item?id=".$_GET['id']);