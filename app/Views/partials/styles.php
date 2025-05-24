<!-- partials: styles -->
<link rel="stylesheet" href="<?= base_url('resources/css/main.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('resources/css/all.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('resources/css/animate.min.css') ?>">

<?php if (isset($styles) && is_array($styles)): ?>
    <?php foreach ($styles as $style): ?>
        <link rel="stylesheet" href="<?= base_url($style) ?>">
    <?php endforeach; ?>
<?php endif; ?>