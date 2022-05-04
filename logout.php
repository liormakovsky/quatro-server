<?php
require_once 'app/helpers.php';
/* Session hijacking - store user ip and user agent + session_regenerate*/
sess_start('bake');
session_destroy();
header('location:signin.php');
exit;
