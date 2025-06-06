<!-- layout: users/user-layout -->
 <?= $this->extend('layout/default') ?>
 <?= $this->section('content') ?>
 <?= view('components/navbar') ?>

<main class="user-page">
    <!-- Scroll Progress Indicator -->
    <div class="scroll-progress-container">
        <div class="scroll-progress-bar" data-scroll-indicator></div>
    </div>
    
    <div class="container is-fluid user-page-content">
        <div class="columns is-gapless user-page-content-inner">
            <div class="column is-narrow pl-6 sidebar is-hidden-mobile sidebar-bg">
                <?= view('components/sidebar') ?>
            </div>
            <div class="column user-main-content">
                <div class="content-page m-2" tabindex="0">
                    <!-- Main Content -->
                    <?= $this->renderSection('content') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Scroll Controls -->
    <div class="floating-scroll-controls">
        <button class="floating-btn scroll-to-top" 
                data-scroll-to-top 
                data-scroll-target=".content-page"
                title="Scroll to Top"
                aria-label="Scroll to Top">
            <i class="fas fa-chevron-up"></i>
        </button>
        
        <button class="floating-btn scroll-to-bottom" 
                data-scroll-to-bottom 
                data-scroll-target=".content-page"
                title="Scroll to Bottom"
                aria-label="Scroll to Bottom">
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>
</main>

<?= view('components/footer') ?>
<?= $this->endSection() ?>
