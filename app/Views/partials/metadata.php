<!-- partials: metadata -->
<?php
// Use extracted variables from the $meta array passed to the view
$title = $title ?? 'Default Page Title';
$description = $description ?? 'Default description for this page.';
$keywords = $keywords ?? 'default, keywords, here';
$image = $image ?? '/default-image.png';
$url = $url ?? (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/');
$siteName = $siteName ?? getenv('app.siteName');
?>
<title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
<meta name="description" content="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>">
<meta name="keywords" content="<?= htmlspecialchars($keywords, ENT_QUOTES, 'UTF-8') ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="UTF-8">

<!-- Open Graph / Facebook -->
<meta property="og:title" content="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
<meta property="og:description" content="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>">
<meta property="og:image" content="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>">
<meta property="og:url" content="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?>">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>">
<meta name="twitter:image" content="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>">