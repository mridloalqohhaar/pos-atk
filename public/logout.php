<?php

require __DIR__ . '/../bootstrap.php';

$auth->keluar();
session_regenerate_id(true);

header('Location: login.php');
exit;