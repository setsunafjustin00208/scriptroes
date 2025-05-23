<!-- pages: homepage -->
 <?= $this->extend('layout/default') ?>
 <?= $this->section('content') ?>
 <main class="homepage">
    <h1 class="title">Welcome, <?= esc($fullname) ?></h1>
    <?php if ($user): ?>
        <section class="user-info box">
            <h2 class="subtitle">Your Info</h2>
            <ul>
                <li><strong>Username:</strong> <?= esc($user['username'] ?? '') ?></li>
                <li><strong>Email:</strong> <?= esc($user['email'] ?? '') ?></li>
                <?php if (isset($user['personal_info'])): ?>
                    <li><strong>Full Name:</strong> <?= esc(($user['personal_info']['first_name'] ?? '') . ' ' . ($user['personal_info']['last_name'] ?? '')) ?></li>
                    <li><strong>Type:</strong> <?= esc($user['type'] ?? '') ?></li>
                <?php endif; ?>
            </ul>
        </section>
    <?php else: ?>
        <div class="notification is-warning">You are not logged in.</div>
    <?php endif; ?>
</main>
 <?= $this->endSection() ?>
