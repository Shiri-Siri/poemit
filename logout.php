<?php
require_once 'config/config.php';

// Destroy session and redirect
session_destroy();
redirect('/login');
?>
