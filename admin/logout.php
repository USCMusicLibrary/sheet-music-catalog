<?php
//account page
session_start();
$_SESSION['logged-in'] = false;

header("Location: login");
die();

//todo: add html and js redirects