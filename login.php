<?php require("head.php");?>

<?php
if($_SESSION["admin"]){ //This will log the user out, if logged in
    $_SESSION["admin"] = false;

}else{ //Otherwise, log in.
print '<aside id="loginFunction">';