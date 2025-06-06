<!-- partials: mobile-side-nav -->
<aside class="menu ___mobile-sidebar p-3 animate__animated" 
    data-target="sidebar_mobile" 
    x-ref="sidebar"
    x-show="sidebarVisible" 
    x-cloak
    @click.away="toggleSidebar()"
    @keydown.escape.window="toggleSidebar()"
    style="will-change: transform; transform: translateX(-100%);">
    <?= $this->include('partials/sidebar-contents') ?>
</aside>
