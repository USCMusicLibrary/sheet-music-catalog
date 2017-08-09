<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
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