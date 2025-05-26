<?php
require_once '../../config/connection.php';
require_once '../../includes/auth.php';
logout();
header('Location: ' . $base_url . '/index.php');
exit();
