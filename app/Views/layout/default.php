<!DOCTYPE html>
<html lang="en">
<head>
    <?= view('partials/metadata', isset($meta) ? $meta : []) ?>
    <?= view('partials/styles', isset($resources['styles']) ? ['styles' => $resources['styles']] : []) ?>
</head>
<body>
    <?= view('partials/global_variables'); ?>
    <?= $this->renderSection('content') ?>
    <?= view('partials/scripts', isset($resources['scripts']) ? ['scripts' => $resources['scripts']] : []) ?>
</body>
</html>