<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
//account page
session_start();
$_SESSION['logged-in'] = false;

header("Location: login");
die();

//todo: add html and js redirects