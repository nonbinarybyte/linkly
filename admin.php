<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'inc/db/db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $name = $_POST['name'];
    $bio = $_POST['bio'];
    $avatar = $_POST['avatar'];
    $links = $_POST['links']; // format: title|url per line

    $stmt = $db->prepare("INSERT INTO users (username, name, bio, avatar_url) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $name, $bio, $avatar]);
    $user_id = $db->lastInsertId();

    foreach (explode("\n", $links) as $line) {
        $parts = explode('|', trim($line));
        if (count($parts) === 2) {
            $stmt = $db->prepare("INSERT INTO links (user_id, title, url) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, trim($parts[0]), trim($parts[1])]);
        }
    }


    echo "User created! <a href='/@$username'>View profile</a>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Add User</title>
  <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
  <h1>Create Profile</h1>
  <form method="POST">
    <input name="username" placeholder="username" required>
    <input name="name" placeholder="Full Name">
    <input name="avatar" placeholder="Avatar URL">
    <textarea name="bio" placeholder="Bio"></textarea>
    <textarea name="links" placeholder="title|url, one per line"></textarea>
    <button type="submit">Create</button>
  </form>
</body>
</html>
