<?php
require_once('../../private/initialize.php');
log_out_user();
//Destroys a current users session after they logout
//session_destroy();
redirect_to('login.php');
?>
