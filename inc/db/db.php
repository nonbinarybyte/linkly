<?php
$db = new PDO('sqlite:' . __DIR__ . '/db/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
