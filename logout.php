<?php
session_start();
require_once __DIR__ . '/User.php';
$user = new User();
$user->logout();
header("Location: index.php");
