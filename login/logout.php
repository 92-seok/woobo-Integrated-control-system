<?php
session_start();

$_SESSION = [];
setcookie(session_name(), '', 1);
session_unset();
session_destroy();

echo "<script>window.location.replace('/index.php')</script>";
//echo "<script>window.location.replace('/index.php')</script>";
