<!-- pages: homepage -->
 <?= $this->extend('layout/users/user-layout') ?>
 <?= $this->section('content') ?>

<div class="homepage-content">
    <!-- Hero Section -->
    <section class="hero is-primary is-small mb-6 p-6">
        <div class="hero-body">
            <div class="container">
                <h1 class="title is-2">
                    <span class="icon-text ">
                        <span class="icon mr-3">
                            <i class="fas fa-home"></i>
                        </span>
                        <span>Welcome back, <?= esc($fullname) ?>!</span>
                    </span>
                </h1>
                <p class="subtitle is-4">
                    Ready to dive into your dashboard? Let's get started! 🚀
                </p>
            </div>
        </div>
    </section>

    <!-- User Information Card -->
    <?php if ($user): ?>
        <section class="user-profile-section mb-6">
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">
                        <span class="icon-text">
                            <span class="icon has-text-info">
                                <i class="fas fa-user-circle"></i>
                            </span>
                            <span>Your Profile Information</span>
                        </span>
                    </p>
                    <button class="card-header-icon" aria-label="more options">
                        <span class="icon">
                            <i class="fas fa-edit" aria-hidden="true"></i>
                        </span>
                    </button>
                </header>
                
                <div class="card-content">
                    <div class="content">
                        <div class="columns is-multiline">
                            <!-- Profile Picture & Basic Info -->
                            <div class="column is-full-mobile is-half-tablet">
                                <div class="media">
                                    <div class="media-left">
                                        <figure class="image is-64x64">
                                            <img class="is-rounded" 
                                                 src="<?= isset($profile_img) && $profile_img ? $profile_img : 'https://placehold.co/128x128/00d1b2/ffffff?text=' . strtoupper(substr($user['username'] ?? 'U', 0, 1)) ?>" 
                                                 alt="Profile Picture">
                                        </figure>
                                    </div>
                                    <div class="media-content">
                                        <div class="field">
                                            <label class="label is-small">Username</label>
                                            <div class="tags has-addons">
                                                <span class="tag is-dark">@</span>
                                                <span class="tag is-info"><?= esc($user['username'] ?? 'N/A') ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="field">
                                            <label class="label is-small">Account Type</label>
                                            <span class="tag is-success is-light">
                                                <span class="icon">
                                                    <i class="fas fa-crown"></i>
                                                </span>
                                                <span><?= esc(ucfirst($user['type'] ?? 'user')) ?></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="column is-full-mobile is-half-tablet">
                                <div class="field">
                                    <label class="label is-small">
                                        <span class="icon-text">
                                            <span class="icon">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <span>Email Address</span>
                                        </span>
                                    </label>
                                    <div class="control">
                                        <p class="has-text-weight-medium has-text-grey-dark">
                                            <?= esc($user['email'] ?? 'N/A') ?>
                                        </p>
                                    </div>
                                </div>

                                <?php if (isset($user['personal_info'])): ?>
                                    <div class="field">
                                        <label class="label is-small">
                                            <span class="icon-text">
                                                <span class="icon">
                                                    <i class="fas fa-id-card"></i>
                                                </span>
                                                <span>Full Name</span>
                                            </span>
                                        </label>
                                        <div class="control">
                                            <p class="has-text-weight-medium has-text-grey-dark">
                                                <?= esc(trim(($user['personal_info']['first_name'] ?? '') . ' ' . ($user['personal_info']['last_name'] ?? '')) ?: 'Not provided') ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <footer class="card-footer">
                    <a href="/profile/edit" class="card-footer-item has-text-info">
                        <span class="icon-text">
                            <span class="icon">
                                <i class="fas fa-edit"></i>
                            </span>
                            <span>Edit Profile</span>
                        </span>
                    </a>
                    <a href="/settings" class="card-footer-item has-text-grey">
                        <span class="icon-text">
                            <span class="icon">
                                <i class="fas fa-cog"></i>
                            </span>
                            <span>Settings</span>
                        </span>
                    </a>
                </footer>
            </div>
        </section>

    <?php else: ?>
        <!-- Not Logged In State -->
        <section class="auth-required-section mb-6">
            <div class="notification is-warning is-light">
                <div class="columns is-vcentered">
                    <div class="column is-narrow">
                        <span class="icon is-large has-text-warning">
                            <i class="fas fa-exclamation-triangle fa-3x"></i>
                        </span>
                    </div>
                    <div class="column">
                        <h3 class="title is-4 has-text-warning-dark">Authentication Required</h3>
                        <p class="subtitle is-6 mb-4">
                            Oops! It looks like you're not logged in. Please sign in to access your personalized dashboard.
                        </p>
                        <div class="buttons">
                            <a href="/login" class="button is-warning">
                                <span class="icon">
                                    <i class="fas fa-sign-in-alt"></i>
                                </span>
                                <span>Sign In</span>
                            </a>
                            <a href="/register" class="button is-light">
                                <span class="icon">
                                    <i class="fas fa-user-plus"></i>
                                </span>
                                <span>Create Account</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Quick Actions Dashboard -->
    <section class="quick-actions-section mb-6">
        <div class="container">
            <header class="section-header mb-5">
                <h2 class="title is-3 has-text-centered">
                    <span class="icon-text">
                        <span class="icon has-text-primary">
                            <i class="fas fa-bolt"></i>
                        </span>
                        <span>Quick Actions</span>
                    </span>
                </h2>
                <p class="subtitle is-5 has-text-centered has-text-grey">
                    Jump right into what you need to do
                </p>
            </header>

            <div class="columns is-multiline">
                <!-- Dashboard Card -->
                <div class="column is-12-mobile is-6-tablet is-4-desktop">
                    <div class="card has-shadow-hover">
                        <div class="card-content has-text-centered">
                            <div class="content">
                                <span class="icon is-large has-text-primary mb-3">
                                    <i class="fas fa-tachometer-alt fa-3x"></i>
                                </span>
                                <h4 class="title is-5">Dashboard</h4>
                                <p class="subtitle is-6 has-text-grey">
                                    View your complete overview and analytics
                                </p>
                            </div>
                        </div>
                        <footer class="card-footer">
                            <a href="/dashboard" class="card-footer-item has-text-primary">
                                <span class="icon-text">
                                    <span>View Dashboard</span>
                                    <span class="icon">
                                        <i class="fas fa-arrow-right"></i>
                                    </span>
                                </span>
                            </a>
                        </footer>
                    </div>
                </div>

                <!-- Settings Card -->
                <div class="column is-12-mobile is-6-tablet is-4-desktop">
                    <div class="card has-shadow-hover">
                        <div class="card-content has-text-centered">
                            <div class="content">
                                <span class="icon is-large has-text-info mb-3">
                                    <i class="fas fa-cog fa-3x"></i>
                                </span>
                                <h4 class="title is-5">Settings</h4>
                                <p class="subtitle is-6 has-text-grey">
                                    Customize your preferences and account
                                </p>
                            </div>
                        </div>
                        <footer class="card-footer">
                            <a href="/settings" class="card-footer-item has-text-info">
                                <span class="icon-text">
                                    <span>Manage Settings</span>
                                    <span class="icon">
                                        <i class="fas fa-arrow-right"></i>
                                    </span>
                                </span>
                            </a>
                        </footer>
                    </div>
                </div>

                <!-- Community Card -->
                <div class="column is-12-mobile is-6-tablet is-4-desktop">
                    <div class="card has-shadow-hover">
                        <div class="card-content has-text-centered">
                            <div class="content">
                                <span class="icon is-large has-text-success mb-3">
                                    <i class="fas fa-user-friends fa-3x"></i>
                                </span>
                                <h4 class="title is-5">Community</h4>
                                <p class="subtitle is-6 has-text-grey">
                                    Connect and collaborate with others
                                </p>
                            </div>
                        </div>
                        <footer class="card-footer">
                            <a href="/community" class="card-footer-item has-text-success">
                                <span class="icon-text">
                                    <span>Join Community</span>
                                    <span class="icon">
                                        <i class="fas fa-arrow-right"></i>
                                    </span>
                                </span>
                            </a>
                        </footer>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Activity (placeholder for future content) -->
    <section class="recent-activity-section">
        <div class="card">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon-text">
                        <span class="icon has-text-grey">
                            <i class="fas fa-clock"></i>
                        </span>
                        <span>Recent Activity</span>
                    </span>
                </p>
            </header>
            <div class="card-content">
                <div class="content has-text-centered py-6">
                    <span class="icon is-large has-text-grey-light mb-3">
                        <i class="fas fa-history fa-3x"></i>
                    </span>
                    <p class="title is-5 has-text-grey">No recent activity</p>
                    <p class="subtitle is-6 has-text-grey-light">
                        Your recent actions will appear here once you start using the platform.
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Custom CSS for hover effects -->
<style>
.has-shadow-hover {
    transition: box-shadow 0.3s ease;
}

.has-shadow-hover:hover {
    box-shadow: 0 8px 16px rgba(10, 10, 10, 0.1), 0 0 0 1px rgba(10, 10, 10, 0.02);
    transform: translateY(-2px);
}

.hero.is-primary {
    background: linear-gradient(135deg, #00d1b2 0%, #00b8a9 100%);
}
</style>

<?= $this->endSection() ?>