<!-- components: sidebar -->
<aside class="menu sidebar-desktop" 
    data-target="sidebar_desktop"
    x-data="{ collapsed: false }"
    :class="{ 'is-collapsed': collapsed }">
    
    <!-- Site title and toggle button -->
    <div class="sidebar-header p-3">
        <!-- Full title when expanded -->
        <div x-show="!collapsed" x-transition class="mb-2">
            <div class="level is-mobile">
                <div class="level-left has-text-centered">
                    <div class="level-item">
                        <h2 class="title is-6 mt-1 ml-1 mb-0"><?= esc(env('app.siteName', 'World of Anime Roleplayers')) ?></h2>
                    </div>
                </div>
                <div class="level-right">
                    <div class="level-item">
                        <button class="button is-small is-ghost" @click="collapsed = !collapsed" title="Collapse sidebar">
                            <span class="icon">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Collapsed state - just toggle button centered -->
        <div x-show="collapsed" x-transition class="has-text-centered">
            <button class="button is-small is-ghost" @click="collapsed = !collapsed" 
                    :title="'Expand sidebar - ' + '<?= esc(env('app.siteName', 'World of Anime Roleplayers')) ?>'">
                <span class="icon">
                    <i class="fas fa-chevron-right"></i>
                </span>
            </button>
        </div>
    </div>
    
    <?= $this->include('partials/sidebar-contents') ?>
</aside>
