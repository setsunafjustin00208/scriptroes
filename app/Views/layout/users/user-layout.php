<!-- pages: homepage -->
 <?= $this->extend('layout/default') ?>
 <?= $this->section('content') ?>
 <?= $this->include('components/navbar') ?>
 <main class="homepage">
    <div class="container is-fluid">
        <div class="columns is-gapless">
            <div class="column is-narrow pl-6 sidebar is-hidden-mobile sidebar-bg">
                <?= $this->include('components/sidebar') ?>
            </div>
            <div class="column">
                <div class="content-page m-2">
                    <?= $this->renderSection('content') ?>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->include('components/footer')?> 
<?= $this->endSection() ?>
