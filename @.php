<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'inc/db.php';

// Debug URL
echo "Debug: Current URL - " . $_SERVER['REQUEST_URI'] . "<br>";

$username = ltrim($_SERVER['REQUEST_URI'], '/@');
echo "Debug: Extracted username - " . $username . "<br>";

try {
    // Get user
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    if (!$stmt->execute([$username])) {
        throw new Exception("User query failed: " . implode(" ", $stmt->errorInfo()));
    }
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<pre>User data: "; print_r($user); echo "</pre>";
    
    if (!$user) {
        http_response_code(404);
        die("User not found");
    }

    // Get links
    $stmt = $db->prepare("SELECT * FROM links WHERE user_id = ?");
    if (!$stmt->execute([$user['id']])) {
        throw new Exception("Links query failed: " . implode(" ", $stmt->errorInfo()));
    }
    $links = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<pre>Links data: "; print_r($links); echo "</pre>";
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>@<?= htmlspecialchars($user['username']) ?></title>
  <!-- Temporarily remove CSS for debugging -->
  <!-- <link rel="stylesheet" href="/assets/style.css"> -->
</head>
<body>
  <?php if (!empty($user['avatar_url'])): ?>
    <img src="<?= htmlspecialchars($user['avatar_url']) ?>" alt="Avatar" class="avatar">
  <?php endif; ?>
  
  <h1>@<?= htmlspecialchars($user['username']) ?></h1>
  
  <p class="bio"><?= !empty($user['bio']) ? nl2br(htmlspecialchars($user['bio'])) : 'No bio yet' ?></p>

  <?php if (!empty($links)): ?>
    <?php foreach ($links as $link): ?>
      <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank" class="link">
        <?= htmlspecialchars($link['title']) ?>
      </a><br>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No links yet</p>
  <?php endif; ?>
</body>
</html>
