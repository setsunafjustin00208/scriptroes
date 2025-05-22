<!-- partials: scripts -->
 
<script src="<?= base_url('resources/js/main.bundle.js')?>" type="module"></script>
<script src="<?= base_url('resources/js/fontawesome.min.js')?>" type="module"></script>
<?php if (isset($scripts) && is_array($scripts)): ?>
    <?php foreach ($scripts as $script): ?>
        <script src="<?= base_url($script) ?>" type="module"></script>
    <?php endforeach; ?>
<?php endif; ?>